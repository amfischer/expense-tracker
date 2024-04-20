dbfresh:
	./vendor/bin/sail artisan migrate:fresh --seed
	./vendor/bin/sail artisan app:refresh-test-data