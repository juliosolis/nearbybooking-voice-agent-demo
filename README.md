# Mirador Azul · voice concierge demo

Independent hotel-domain portfolio demo. **All hotel, guest and request data is fictional.** No NearbyBooking source or live booking system is used. The simulator makes no phone call; a request is never a confirmed reservation.

## Stack and features

- CodeIgniter 4.7 + SQLite: bilingual conversation state, FAQs, call log and request queue.
- Shield: login-protected admin dashboard, with public registration disabled.
- AdminLTE 4: adapted dashboard to edit ES/EN answers and review requests.
- Twilio Voice: `<Gather input="speech dtmf">` and `<Say>` turn flow in `es-MX` or `en-US`, with `X-Twilio-Signature` validation on every webhook.
- Optional Gemini Developer API: answers constrained to fictitious hotel facts. Missing key, errors and non-200 responses fall back to local FAQs or an honest unknown answer.

## Local setup

Requirements: PHP 8.2+, Composer, SQLite extensions.

```powershell
composer install
Copy-Item .env.example .env
php spark migrate --all
php spark db:seed DemoSeeder
php spark serve --host 127.0.0.1 --port 8081
```

Open `http://127.0.0.1:8081`, start a simulated call and say `español` or `English` (or press 1/2 in Twilio). Ask about `estacionamiento`/`parking`, then say `reserva`/`reservation`, a test name and a preferred date. The simulator and Twilio use the same `Conversation` class. The `.env` and SQLite database are ignored by Git.

To enable admin, create a local Shield account with `php spark shield:user create -n admin -e admin@example.test` and follow the password prompt. Open `/login` and then `/admin`. Public self-registration is disabled. Do not use a real email or password from another account.

## Optional Twilio trial and Gemini

1. Confirm the *current* Twilio trial balance and restrictions in your own console before a call. Use only a verified destination number. Do not enable billing for this demo.
2. Expose the local server temporarily over HTTPS (for example with a tunnel). Set `TWILIO_PUBLIC_BASE_URL` to that exact public origin, without a trailing slash, and `TWILIO_AUTH_TOKEN` to your trial auth token in ignored `.env`.
3. Configure the trial number's incoming voice webhook as `POST https://<public-origin>/voice/incoming`. The app forms `/voice/turn` from the same origin. A missing or invalid Twilio signature returns `403`.
4. Optionally set `GEMINI_API_KEY` in `.env` for factual answers about this fictitious hotel. No key is required for the local FAQ flow. Never expose this server key in a browser or commit it.

The Twilio webhook URL must match the externally visible URL used for signing. If a reverse proxy rewrites the path, adjust the public URL configuration. Avoid including real guest data in the Gemini request or the SQLite demo database.

## Verification

```powershell
vendor\bin\phpunit --no-coverage tests\VoiceFlowTest.php
```

The automated tests cover Spanish, English, keypad language choice, FAQ answers, invalid empty speech and a stored request. Browser smoke testing covered an English request and the public admin redirect. A locally signed mock webhook returned `200` and TwiML `<Gather>`; an unsigned webhook returned `403`. A live Twilio trial call and Gemini quota/error behavior still require provider credentials and were not claimed as completed. The local, silent browser walkthrough is in `videos/voice-concierge-demo.webm`.

## Visual assets

The coastal hotel photograph was generated for this fictional project, optimized as `public/images/coast.webp` and reused in the simulator and admin panel. Matching SVG, ICO and PNG favicons, app icons and social preview are under `public`. It does not depict an operating hotel.
