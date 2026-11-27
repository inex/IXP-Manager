# Optional dedicated IRRDB queue

Manual IRRDB updates can run outside the HTTP request without changing the application's default queue connection. Set `IRRDB_QUEUE_CONNECTION=irrdb`, run database migrations, and rebuild cached configuration. Supervise a worker as the web application user:

```
php artisan queue:work irrdb --queue=irrdb --sleep=2 --tries=1 --timeout=1500 --memory=768 --max-time=3600
```

The dedicated connection uses the `irrdb_jobs` table and a retry interval of 1800 seconds. Keep worker timeout below the retry interval. Restart workers after deploying changes. A persistent cache supporting atomic locks is required; configure the same cache for web, worker, and scheduled IRRDB imports.

Leaving the environment variable unset preserves existing queue selection. Pagination, batched writes, and writer serialization apply independently. Listing search matches the start of a prefix or ASN. Change previews are limited to 50 entries with exact totals retained. Failed imports roll back their transaction and leave previously committed records available.

The worker must be supervised and monitored: jobs cannot complete while it is stopped. This feature does not reload route-server configurations or restart BGP sessions.
