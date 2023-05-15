echo 'Building com_volunteers'
composer install
./vendor/bin/robo build
cp templates/*.zip dist/


