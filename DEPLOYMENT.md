# 🚀 cPanel Deployment Guide - Traveleye Bangla

## Prerequisites
- cPanel access with PHP 8.1+
- MySQL database created
- SSL certificate installed (Let's Encrypt via cPanel)

---

## Step 1: Prepare Database in cPanel

1. **Login to cPanel** → **MySQL Databases**
2. **Create Database**: `traveleyeba_db`
3. **Create User**: `traveleyeba_traveleyeba` + **Strong Password**
4. **Add User to Database**: Grant **ALL PRIVILEGES**
5. **Note down**: Database name, username, password

---

## Step 2: Upload Files via Git/GitHub

### Option A: Git Version Control (cPanel)
1. cPanel → **Git Version Control** → **Create**
2. Repository URL: `https://github.com/YOUR_USERNAME/YOUR_REPO.git`
3. Branch: `main` (or `master`)
4. Deploy Path: `public_html` (or your domain's document root)
5. Click **Create** → **Pull/Deploy**

### Option B: Manual Upload
1. cPanel → **File Manager** → `public_html`
2. Upload all files (or extract zip)
3. **Important**: Upload `.htaccess` (hidden file - enable "Show Hidden Files")

---

## Step 3: Configure Environment

### In File Manager → `public_html`:
1. **Copy** `.env.example` → **Rename** to `.env`
2. **Edit `.env`** with your values:

```env
APP_NAME="Traveleye Bangla"
APP_URL="https://traveleyebangla.com"
APP_DEBUG=false
APP_TIMEZONE=Asia/Dhaka

DB_HOST=localhost
DB_PORT=3306
DB_NAME=traveleyeba_db
DB_USER=traveleyeba_traveleyeba
DB_PASS=your_actual_cpanel_db_password

ADMIN_EMAIL=admin@traveleyebangla.com
ADMIN_PASSWORD=ChangeMe123!
```

---

## Step 4: Import Database

### Option A: Via Browser (Easiest)
Visit: `https://traveleyebangla.com/import-db.php?confirm=yes`

### Option B: Via cPanel Terminal/SSH
```bash
cd public_html
mysql -u traveleyeba_traveleyeba -p traveleyeba_db < database.sql
```

### Option C: phpMyAdmin
1. cPanel → **phpMyAdmin** → Select `traveleyeba_db`
2. **Import** tab → Choose `database.sql` → **Go**

---

## Step 5: Set File Permissions

### cPanel File Manager:
1. Select `assets/uploads` folder → **Permissions** → `755`
2. Select all subfolders/files in `assets/uploads` → **Permissions** → `755` (folders) / `644` (files)

### Via Terminal:
```bash
chmod -R 755 assets/uploads
find assets/uploads -type f -exec chmod 644 {} \;
```

---

## Step 6: Verify PHP Extensions

cPanel → **Select PHP Version** → **Extensions** → Enable:
- ☑ `pdo_mysql`
- ☑ `mbstring`
- ☑ `gd`
- ☑ `fileinfo`
- ☑ `curl`
- ☑ `json`
- ☑ `openssl`
- ☑ `zip` (optional)

**PHP Version**: Select **8.1** or **8.2** → **Set as current**

---

## Step 7: Enable SSL (HTTPS)

1. cPanel → **SSL/TLS Status** → **Run AutoSSL**
2. Or: **Let's Encrypt SSL** → **Issue**
3. Force HTTPS in `.htaccess` (already configured - uncomment lines 9-10)

---

## Step 8: Run Health Check

Visit: `https://traveleyebangla.com/deploy.php`

**All checks should pass** ✅

If errors:
- **Database Connection**: Check `.env` credentials
- **Missing Tables**: Re-run import-db.php
- **Extensions**: Enable in Select PHP Version
- **Permissions**: Fix via File Manager

---

## Step 9: Clean Up (REQUIRED)

```bash
# Delete these files after successful deployment:
rm deploy.php
rm import-db.php
```

**Or via File Manager**: Right-click → **Delete**

---

## Step 10: Test Site

1. **Frontend**: `https://traveleyebangla.com/`
2. **Admin**: `https://traveleyebangla.com/admin/login.php`
   - Email: `admin@traveleyebangla.com`
   - Password: `ChangeMe123!`
3. **Change admin password immediately** after first login!

---

## Common Issues & Fixes

| Error | Solution |
|-------|----------|
| **500 Internal Server Error** | Check `.env` exists with correct DB credentials; check error log |
| **Database connection failed** | Verify DB_NAME, DB_USER, DB_PASS in `.env` match cPanel exactly |
| **Tables don't exist** | Run `import-db.php?confirm=yes` or import via phpMyAdmin |
| **CSS/JS not loading** | Check `.htaccess` RewriteBase is `/` (not `/Tamim/`) |
| **Admin login fails** | Ensure admin user exists in database (run import) |
| **File upload fails** | `chmod 755 assets/uploads` and subdirectories |
| **mod_rewrite not working** | Ensure `.htaccess` uploaded; contact host if needed |

---

## GitHub Actions Auto-Deploy (Optional)

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to cPanel
on:
  push:
    branches: [main]
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Deploy via FTP
        uses: SamKirkland/FTP-Deploy-Action@v4.3.5
        with:
          server: ftp.traveleyebangla.com
          username: ${{ secrets.FTP_USER }}
          password: ${{ secrets.FTP_PASS }}
          local-dir: ./
          server-dir: public_html/
          exclude: |
            **/.git*
            **/.env*
            **/database.sql
            **/deploy.php
            **/import-db.php
            **/DEPLOYMENT.md
```

---

## Support

If issues persist:
1. Check **cPanel → Errors** log
2. Run `deploy.php` for diagnostics
3. Verify PHP version is 8.1+
4. Contact hosting support for mod_rewrite/extension issues