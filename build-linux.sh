echo 'Building com_volunteers'
composer install
./vendor/bin/robo build
cp templates/*.zip dist/

echo 'Building the development seeder plg_console_volunteers, not part of the package'
rm -f dist/plg_console_volunteers.zip
(cd dev/plugins/console/volunteers && zip -qr ../../../../dist/plg_console_volunteers.zip .)
