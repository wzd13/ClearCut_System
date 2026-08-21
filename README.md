# ClearCut — AI Background Removal

**ClearCut** is an AI-powered web application that removes image backgrounds instantly. Upload any photo, let the [Pixelcut API](https://developer.pixelcut.ai/) do the work, and download a crisp transparent PNG — no design skills or software required.

Built with **PHP, HTML, CSS, and Vanilla JavaScript**. No frameworks, no database, no package managers.

---

## Screenshots

**Home Page**

![ClearCut Home Page](docs/screenshots/home.png)

**User Manual**

![ClearCut User Manual](docs/screenshots/manual.png)

---

## Features

- Drag-and-drop or click-to-browse image upload
- Client-side and server-side file validation
- Real-time processing status with stage indicators
- Side-by-side comparison: original vs. background-removed
- Checkerboard transparency preview
- Secure transparent PNG download (`no-bg.png`)
- Automatic cleanup of temporary files (uploads deleted immediately; results after 1 hour)
- Responsive design — works on desktop, tablet, and mobile
- No database required
- No framework dependencies

---

## Technology Stack

| Layer      | Technology                      |
|------------|---------------------------------|
| Frontend   | HTML5, CSS3, Vanilla JavaScript |
| Backend    | PHP 8+, PHP cURL                |
| AI Engine  | Pixelcut Background Removal API |
| Database   | None                            |

---

## Project Structure

```
clearcut/
│
├── index.php           — Main application page
├── manual.php          — User Manual page
├── config.php          — API key & app config  ⚠️ excluded by .gitignore
├── README.md           — This file
├── LICENSE             — MIT License
├── .gitignore          — Excludes secrets, uploads/, outputs/, IDE files
├── .htaccess           — Root security headers & access rules
│
├── api/
│   ├── process.php     — Validates upload, calls Pixelcut API, returns JSON
│   └── download.php    — Serves result PNG inline (preview) or as download
│
├── assets/
│   ├── css/
│   │   ├── style.css   — Main styles
│   │   └── manual.css  — User Manual styles
│   └── js/
│       ├── app.js      — Upload, AJAX, result display logic
│       └── manual.js   — Smooth scroll for the manual page
│
├── docs/
│   └── screenshots/    — README screenshots
│
├── includes/
│   └── footer.php      — Shared page footer
│
├── uploads/            — Temp uploaded images (auto-deleted)  ⚠️ excluded by .gitignore
│   └── .htaccess
│
└── outputs/            — Result PNGs (auto-deleted after 1 h)  ⚠️ excluded by .gitignore
    └── .htaccess       — Blocks direct HTTP; served only via api/download.php
```

---

## Requirements

- PHP 8.0 or higher
- PHP cURL extension enabled
- Modern web browser (Chrome, Firefox, Edge, Safari)
- Internet connection (Pixelcut is a cloud API)
- A free [Pixelcut API key](https://developer.pixelcut.ai/)
- Apache / XAMPP, Nginx, or PHP built-in server

---

## Quick Start (XAMPP)

1. **Clone or download** the project into `C:\xampp\htdocs\clearcut\`

2. **Add your Pixelcut API key** — open `config.php` and set:
   ```php
   define('PIXELCUT_API_KEY', getenv('PIXELCUT_API_KEY') ?: 'sk_your_key_here');
   ```
   > ⚠️ `config.php` is in `.gitignore`. Never commit a real key to GitHub.

3. **Enable PHP cURL** in `C:\xampp\php\php.ini`:
   ```
   extension=curl
   ```
   Restart Apache after saving.

4. **Open** `http://localhost/clearcut/` in your browser.

---

## Configuration

`config.php` is the single place for all settings:

| Constant | Description |
|---|---|
| `PIXELCUT_API_KEY` | Your Pixelcut API key |
| `PIXELCUT_API_ENDPOINT` | API URL (do not change) |
| `MAX_FILE_SIZE` | Upload limit in bytes (default 8 MB) |
| `ALLOWED_MIME_TYPES` | Accepted image MIME types |
| `UPLOAD_DIR` | Path to temp upload folder |
| `OUTPUT_DIR` | Path to processed result folder |

**Recommended: use an environment variable instead of editing `config.php`:**

```apache
# Add to root .htaccess or your hosting panel
SetEnv PIXELCUT_API_KEY your_actual_api_key_here
```

---

## How It Works

1. The browser sends the image to `api/process.php` via `multipart/form-data`.
2. PHP validates the file (MIME type, extension, size).
3. The file is sent **directly** to the Pixelcut API as a binary upload — no public URL needed.
4. Pixelcut returns a `result_url`; PHP downloads the PNG and saves it to `outputs/`.
5. The browser loads the preview via `api/download.php?id=<id>&display=1`.
6. The **Download PNG** button triggers `api/download.php?id=<id>` which streams the file as an attachment.

---

## Security

- The API key lives only in `config.php` (server-side) — never sent to the browser.
- Uploaded files use **random, unguessable filenames** — the original name is never used on the server.
- `uploads/` and `outputs/` block directory listing and PHP execution via `.htaccess`.
- `outputs/` blocks all direct HTTP access — files are served exclusively through PHP.
- The download endpoint validates the result ID with a strict regex and uses `realpath()` to prevent directory traversal.
- File MIME types are validated server-side with `finfo_file()` — never trusting `Content-Type`.

---

## Troubleshooting

| Problem | Fix |
|---|---|
| "Unable to connect to Pixelcut" | PHP cURL can't reach the API. Check internet, SSL cert (`curl.cainfo` in `php.ini`), firewall. |
| "Pixelcut API request failed" | API returned non-200. Check your API key and remaining credits. |
| Upload fails | Ensure `upload_max_filesize` and `post_max_size` ≥ `8M` in `php.ini`. |
| Result image shows broken | Hard-refresh the page (Ctrl+Shift+R) to clear cached JS. |
| Download fails / not found | Results expire after 1 hour. Re-process the image. |

Check `C:\xampp\php\logs\php_error_log` for `[ClearCut]` entries for detailed diagnostics.

---

## License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.
