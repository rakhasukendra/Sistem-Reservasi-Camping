#!/usr/bin/env node

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');
const readline = require('readline');

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

const question = (q) => new Promise(resolve => rl.question(q, resolve));

async function main() {
  console.log('\n🚀 Reservasi Camping - Vercel Database Setup\n');
  console.log('='.repeat(50));

  try {
    // Step 1: Cek Supabase credentials
    console.log('\n📝 Masukkan Supabase Database Credentials:\n');

    const DB_HOST = await question('DB_HOST (e.g., db.xxxxx.supabase.co): ');
    const DB_PORT = await question('DB_PORT [5432]: ') || '5432';
    const DB_DATABASE = await question('DB_DATABASE [postgres]: ') || 'postgres';
    const DB_USERNAME = await question('DB_USERNAME [postgres]: ') || 'postgres';
    const DB_PASSWORD = await question('DB_PASSWORD: ');
    
    if (!DB_HOST || !DB_PASSWORD) {
      console.log('\n❌ Host dan password harus diisi!');
      process.exit(1);
    }

    console.log('\n✅ Credentials diterima\n');

    // Step 2: Update .env.production
    console.log('📝 Updating .env.production...');
    const envProductionPath = path.join(__dirname, '..', '.env.production');
    
    const envContent = `APP_NAME=Laravel
APP_ENV=production
APP_KEY=base64:RPkwnWWcEGQ1WC1WoYYR8PIy7hrFE+j8TVS3SupotfE=
APP_DEBUG=false
APP_URL=https://reservasi-camping.vercel.app

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stderr
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# PostgreSQL Database (Supabase)
DB_CONNECTION=pgsql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
DB_SSLMODE=require

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync

CACHE_STORE=database

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="\${APP_NAME}"

VITE_APP_NAME="\${APP_NAME}"
`;

    fs.writeFileSync(envProductionPath, envContent);
    console.log('✅ .env.production updated\n');

    // Step 3: Set Vercel Environment Variables
    console.log('🔧 Setting Vercel environment variables...');
    
    try {
      const envVars = {
        DB_CONNECTION: 'pgsql',
        DB_HOST,
        DB_PORT,
        DB_DATABASE,
        DB_USERNAME,
        DB_PASSWORD,
        DB_SSLMODE: 'require',
        APP_ENV: 'production',
        APP_DEBUG: 'false',
        LOG_CHANNEL: 'stderr'
      };

      for (const [key, value] of Object.entries(envVars)) {
        try {
          console.log(`  Setting ${key}...`);
          execSync(`vercel env add ${key} production`, {
            input: value,
            stdio: 'pipe'
          });
        } catch (e) {
          console.log(`  ⚠️  ${key} - mungkin sudah ada`);
        }
      }
      console.log('✅ Vercel env vars set\n');
    } catch (e) {
      console.log('⚠️  Vercel CLI error. Manual set diperlukan.\n');
      console.log('Jalankan commands ini di terminal:\n');
      
      const envVars = {
        DB_CONNECTION: 'pgsql',
        DB_HOST,
        DB_PORT,
        DB_DATABASE,
        DB_USERNAME,
        DB_PASSWORD,
        DB_SSLMODE: 'require',
        APP_ENV: 'production',
        APP_DEBUG: 'false',
        LOG_CHANNEL: 'stderr'
      };

      for (const [key, value] of Object.entries(envVars)) {
        console.log(`vercel env add ${key} production`);
        console.log(`# Input: ${value}\n`);
      }
    }

    // Step 4: Git commit
    console.log('📦 Committing to git...');
    try {
      execSync('git add .env.production setup-vercel-db.md scripts/', { cwd: path.join(__dirname, '..') });
      execSync('git commit -m "Setup PostgreSQL database for production"', { cwd: path.join(__dirname, '..') });
      console.log('✅ Committed\n');
    } catch (e) {
      console.log('⚠️  Git commit failed (mungkin tidak ada changes)\n');
    }

    // Step 5: Offer to push
    const push = await question('Push ke GitHub dan trigger deployment? (y/n): ');
    
    if (push.toLowerCase() === 'y') {
      console.log('\n🚀 Pushing to GitHub...');
      execSync('git push', { cwd: path.join(__dirname, '..'), stdio: 'inherit' });
      console.log('\n✅ Pushed! Deployment triggered di Vercel\n');
    }

    // Step 6: Offer to run migrations
    const migrate = await question('Run migrations sekarang? (y/n): ');
    
    if (migrate.toLowerCase() === 'y') {
      console.log('\n🔄 Running migrations...');
      
      // Update local .env temporarily
      const envPath = path.join(__dirname, '..', '.env');
      const originalEnv = fs.readFileSync(envPath, 'utf8');
      
      const tempEnv = originalEnv.replace(/^DB_CONNECTION=.*/m, 'DB_CONNECTION=pgsql')
        .replace(/^# DB_HOST=.*/m, `DB_HOST=${DB_HOST}`)
        .replace(/^# DB_PORT=.*/m, `DB_PORT=${DB_PORT}`)
        .replace(/^# DB_DATABASE=.*/m, `DB_DATABASE=${DB_DATABASE}`)
        .replace(/^# DB_USERNAME=.*/m, `DB_USERNAME=${DB_USERNAME}`)
        .replace(/^# DB_PASSWORD=.*/m, `DB_PASSWORD=${DB_PASSWORD}`);
      
      fs.writeFileSync(envPath, tempEnv);
      
      try {
        execSync('php artisan migrate --force', { 
          cwd: path.join(__dirname, '..'),
          stdio: 'inherit'
        });
        console.log('\n✅ Migrations complete!\n');
      } catch (e) {
        console.log('\n❌ Migration failed');
      }
      
      // Restore original .env
      fs.writeFileSync(envPath, originalEnv);
    }

    console.log('='.repeat(50));
    console.log('\n✨ Setup Complete!\n');
    console.log('Cek: https://reservasi-camping.vercel.app\n');
    console.log('Jika ada error, lihat setup-vercel-db.md untuk troubleshooting\n');

  } catch (error) {
    console.error('\n❌ Error:', error.message);
    process.exit(1);
  } finally {
    rl.close();
  }
}

main();
