<?php

/**
 * @package     Joomla.Plugin
 * @subpackage  Console.volunteers
 *
 * @copyright   (C) 2026 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Plugin\Console\Volunteers\Console;

use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseDriver;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Fills a development site with the test data of the Joomla Volunteer Portal
 *
 * @since  6.1.0
 */
class SeedCommand extends AbstractCommand implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * The default command name
     *
     * @var    string
     * @since  6.1.0
     */
    protected static $defaultName = 'volunteers:seed';

    /**
     * The tables of the Joomla Volunteer Portal that get test data
     *
     * @var    array
     * @since  6.1.0
     */
    private const TABLES = [
        '#__volunteers_departments',
        '#__volunteers_positions',
        '#__volunteers_roles',
        '#__volunteers_members',
        '#__volunteers_teams',
        '#__volunteers_volunteers',
        '#__volunteers_reports',
    ];

    /**
     * The user tables that refer to a user ID, used to move a Super User out of the way of the test users
     *
     * @var    array
     * @since  6.1.0
     */
    private const USER_TABLES = [
        '#__users'               => 'id',
        '#__user_usergroup_map'  => 'user_id',
        '#__user_profiles'       => 'user_id',
        '#__user_mfa'            => 'user_id',
        '#__action_logs'         => 'user_id',
        '#__session'             => 'userid',
    ];

    /**
     * The options of the Board of Directors that match the test data
     *
     * @var    array
     * @since  6.1.0
     */
    private const BOARD_OPTIONS = [
        'board_department' => 58,
        'board_positions'  => [11],
        'board_roles'      => [286, 287, 288, 289],
    ];

    /**
     * The email domain of the test users
     *
     * @var    string
     * @since  6.1.0
     */
    private const TEST_EMAIL = '%@dummy.com';

    /**
     * The folder with the SQL files of the test data
     *
     * @var    string
     * @since  6.1.0
     */
    private string $sqlPath = '';

    /**
     * Set the folder with the SQL files of the test data.
     *
     * @param   string  $sqlPath  The folder with the SQL files
     *
     * @return  void
     *
     * @since   6.1.0
     */
    public function setSqlPath(string $sqlPath): void
    {
        $this->sqlPath = $sqlPath;
    }

    /**
     * Configure the command.
     *
     * @return  void
     *
     * @since   6.1.0
     */
    protected function configure(): void
    {
        $this->setDescription('Fill a development site with the test data of the Joomla Volunteer Portal');
        $this->addOption('force', null, InputOption::VALUE_NONE, 'Remove the existing volunteers data and test users before seeding');
        $this->addOption('skip-reports', null, InputOption::VALUE_NONE, 'Do not add the reports, the largest part of the test data');
        $this->setHelp(
            <<<'EOF'
The <info>%command.name%</info> command adds test users, departments, teams, roles, Joomlers and reports
to an empty Joomla Volunteer Portal. The test users have an e-mail address at dummy.com.

  <info>php %command.full_name%</info>

Never run this command on a live site.
EOF
        );
    }

    /**
     * Internal function to execute the command.
     *
     * @param   InputInterface   $input   The input to inject into the command.
     * @param   OutputInterface  $output  The output to inject into the command.
     *
     * @return  integer  The command exit code
     *
     * @since   6.1.0
     */
    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $io          = new SymfonyStyle($input, $output);
        $db          = $this->getDatabase();
        $force       = (bool) $input->getOption('force');
        $skipReports = (bool) $input->getOption('skip-reports');

        $io->title('Seed the Joomla Volunteer Portal');

        $files = glob($this->sqlPath . '/*.sql') ?: [];
        sort($files);

        if ($skipReports) {
            $files = array_filter($files, static fn (string $file): bool => !str_contains(basename($file), 'reports'));
        }

        if (!$files) {
            $io->error('No SQL files found in ' . $this->sqlPath);

            return Command::FAILURE;
        }

        // Only seed an empty portal, unless asked to remove the existing data
        $filled = array_filter(array_map(fn (string $table): int => $this->countRows($table), array_combine(self::TABLES, self::TABLES)));

        if ($filled && !$force) {
            $io->error(
                array_merge(
                    ['The volunteers tables already contain data, use --force to remove it first:'],
                    array_map(static fn (string $table, int $rows): string => $table . ': ' . $rows . ' rows', array_keys($filled), $filled)
                )
            );

            return Command::FAILURE;
        }

        // The test users must not overwrite existing users
        $testUserIds = $this->getTestUserIds($files[0]);
        $colliding   = $this->getCollidingUsers($testUserIds, $force);
        $superUsers  = array_filter($colliding, static fn (object $user): bool => $user->superUser);
        $otherUsers  = array_filter($colliding, static fn (object $user): bool => !$user->superUser);

        if ($otherUsers) {
            $io->error(
                array_merge(
                    ['These users have the ID of a test user and would be overwritten:'],
                    array_map(static fn (object $user): string => $user->id . ': ' . $user->username, $otherUsers)
                )
            );

            return Command::FAILURE;
        }

        $io->listing(
            array_filter(
                [
                    'Database: ' . $this->getApplication()->get('db') . ' (prefix ' . $db->getPrefix() . ')',
                    \count($testUserIds) . ' test users and the departments, positions, roles, teams and Joomlers' . ($skipReports ? '' : ' and reports'),
                    $filled ? 'Removes the existing volunteers data and test users first' : '',
                    'Sets the Board of Directors options of the component to the test data',
                    $superUsers ? 'Moves Super User ' . implode(', ', array_map(static fn (object $user): string => $user->username, $superUsers)) . ' to a free user ID below 30' : '',
                ]
            )
        );

        if ($input->isInteractive() && !$io->confirm('Add the test data?', false)) {
            $io->note('Nothing has been changed.');

            return Command::SUCCESS;
        }

        $db->transactionStart();

        try {
            if ($force) {
                $this->removeData($testUserIds);
            }

            foreach ($superUsers as $superUser) {
                $this->moveUser($superUser->id, $this->getFreeUserId());
            }

            foreach ($files as $file) {
                $this->importFile($file, $io);
            }

            $this->setBoardOptions();

            $db->transactionCommit();
        } catch (\Throwable $exception) {
            $db->transactionRollback();
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        $io->success('The test data has been added.');
        $io->note('Build the Smart Search index to find the test data: php cli/joomla.php finder:index');

        return Command::SUCCESS;
    }

    /**
     * Count the rows of a table.
     *
     * @param   string  $table  The table name
     *
     * @return  integer
     *
     * @since   6.1.0
     */
    private function countRows(string $table): int
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select('COUNT(*)')
            ->from($db->quoteName($table));

        return (int) $db->setQuery($query)->loadResult();
    }

    /**
     * Get the IDs of the test users from the users SQL file.
     *
     * @param   string  $file  The SQL file with the test users
     *
     * @return  array
     *
     * @since   6.1.0
     */
    private function getTestUserIds(string $file): array
    {
        preg_match_all("/^\s*\((\d+), 'Test User/m", (string) file_get_contents($file), $matches);

        return array_map('intval', $matches[1]);
    }

    /**
     * Get the existing users that have the ID of a test user.
     *
     * @param   array    $testUserIds  The IDs of the test users
     * @param   boolean  $force        Leave out earlier test users, they get removed
     *
     * @return  array
     *
     * @since   6.1.0
     */
    private function getCollidingUsers(array $testUserIds, bool $force): array
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select(
                [
                    $db->quoteName('u.id'),
                    $db->quoteName('u.username'),
                    'MAX(' . $db->quoteName('m.group_id') . ' = 8) AS ' . $db->quoteName('superUser'),
                ]
            )
            ->from($db->quoteName('#__users', 'u'))
            ->leftJoin($db->quoteName('#__user_usergroup_map', 'm'), $db->quoteName('m.user_id') . ' = ' . $db->quoteName('u.id'))
            ->whereIn($db->quoteName('u.id'), $testUserIds)
            ->group([$db->quoteName('u.id'), $db->quoteName('u.username')]);

        if ($force) {
            $query->where($db->quoteName('u.email') . ' NOT LIKE ' . $db->quote(self::TEST_EMAIL));
        }

        $users = $db->setQuery($query)->loadObjectList();

        foreach ($users as $user) {
            $user->id        = (int) $user->id;
            $user->superUser = (bool) $user->superUser;
        }

        return $users;
    }

    /**
     * Remove the volunteers data and the earlier test users.
     *
     * @param   array  $testUserIds  The IDs of the test users
     *
     * @return  void
     *
     * @since   6.1.0
     */
    private function removeData(array $testUserIds): void
    {
        $db = $this->getDatabase();

        foreach (self::TABLES as $table) {
            $db->setQuery($db->createQuery()->delete($db->quoteName($table)))->execute();
        }

        $query = $db->createQuery()
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__users'))
            ->whereIn($db->quoteName('id'), $testUserIds)
            ->where($db->quoteName('email') . ' LIKE ' . $db->quote(self::TEST_EMAIL));
        $userIds = $db->setQuery($query)->loadColumn();

        if (!$userIds) {
            return;
        }

        foreach (['#__user_usergroup_map' => 'user_id', '#__users' => 'id'] as $table => $column) {
            $query = $db->createQuery()
                ->delete($db->quoteName($table))
                ->whereIn($db->quoteName($column), $userIds);
            $db->setQuery($query)->execute();
        }
    }

    /**
     * Get the lowest free user ID below 30, the first ID of the test users.
     *
     * @return  integer
     *
     * @since   6.1.0
     *
     * @throws  \RuntimeException
     */
    private function getFreeUserId(): int
    {
        $db    = $this->getDatabase();
        $query = $db->createQuery()
            ->select($db->quoteName('id'))
            ->from($db->quoteName('#__users'))
            ->where($db->quoteName('id') . ' < 30');
        $usedIds = array_map('intval', $db->setQuery($query)->loadColumn());
        $freeIds = array_diff(range(1, 29), $usedIds);

        if (!$freeIds) {
            throw new \RuntimeException('There is no free user ID below 30 to move the Super User to.');
        }

        return min($freeIds);
    }

    /**
     * Move a user to another user ID.
     *
     * @param   integer  $fromId  The current user ID
     * @param   integer  $toId    The new user ID
     *
     * @return  void
     *
     * @since   6.1.0
     */
    private function moveUser(int $fromId, int $toId): void
    {
        $db = $this->getDatabase();

        foreach (self::USER_TABLES as $table => $column) {
            $query = $db->createQuery()
                ->update($db->quoteName($table))
                ->set($db->quoteName($column) . ' = :toId')
                ->where($db->quoteName($column) . ' = :fromId')
                ->bind(':toId', $toId, ParameterType::INTEGER)
                ->bind(':fromId', $fromId, ParameterType::INTEGER);
            $db->setQuery($query)->execute();
        }
    }

    /**
     * Set the Board of Directors options of the component to the department, positions and roles of the test data.
     *
     * @return  void
     *
     * @since   6.1.0
     */
    private function setBoardOptions(): void
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select($db->quoteName('params'))
            ->from($db->quoteName('#__extensions'))
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_volunteers'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'));

        $params = new Registry($db->setQuery($query)->loadResult());
        $params->loadArray(self::BOARD_OPTIONS);
        $value = $params->toString();

        $query = $db->getQuery(true)
            ->update($db->quoteName('#__extensions'))
            ->set($db->quoteName('params') . ' = :params')
            ->where($db->quoteName('element') . ' = ' . $db->quote('com_volunteers'))
            ->where($db->quoteName('type') . ' = ' . $db->quote('component'))
            ->bind(':params', $value);

        $db->setQuery($query)->execute();
    }

    /**
     * Run the queries of a SQL file.
     *
     * @param   string        $file  The SQL file
     * @param   SymfonyStyle  $io    The output style
     *
     * @return  void
     *
     * @since   6.1.0
     */
    private function importFile(string $file, SymfonyStyle $io): void
    {
        $db      = $this->getDatabase();
        $queries = array_filter(array_map('trim', DatabaseDriver::splitSql((string) file_get_contents($file))));

        $io->text(basename($file));
        $io->progressStart(\count($queries));

        foreach ($queries as $query) {
            $db->setQuery($query)->execute();
            $io->progressAdvance();
        }

        $io->progressFinish();
    }
}
