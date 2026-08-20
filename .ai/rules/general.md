---
paths:
  - .env
---

# General

## Restart composer run dev after editing .env
The `composer run dev` multiplex tree loads .env once at startup; its queue:listen children inherit that environment, and Dotenv is immutable, so mail/queue settings edited in .env are silently ignored by the running listener (jobs fail against the old MAIL_HOST). After any .env change, stop and restart `composer run dev`. Kill with `pkill -f "php artisan queue:listen"` then `pkill -f "php artisan queue:work"` (killing only the listener leaves stale --once workers that steal jobs with old env).
