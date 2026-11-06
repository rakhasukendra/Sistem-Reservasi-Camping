#!/bin/bash

# Setup Database Script for Vercel

echo "🚀 Setup Database untuk Vercel Production"
echo "=========================================="
echo ""

# Check if vercel CLI installed
if ! command -v vercel &> /dev/null; then
    echo "❌ Vercel CLI belum installed"
    echo "Run: npm i -g vercel"
    exit 1
fi

echo "📝 Masukkan kredensial Supabase Database:"
echo ""

read -p "DB_HOST (e.g., db.xxx.supabase.co): " DB_HOST
read -p "DB_PORT [5432]: " DB_PORT
DB_PORT=${DB_PORT:-5432}

read -p "DB_DATABASE [postgres]: " DB_DATABASE
DB_DATABASE=${DB_DATABASE:-postgres}

read -p "DB_USERNAME [postgres]: " DB_USERNAME
DB_USERNAME=${DB_USERNAME:-postgres}

read -sp "DB_PASSWORD: " DB_PASSWORD
echo ""
echo ""

echo "🔧 Setting environment variables di Vercel..."

vercel env add DB_CONNECTION production <<< "pgsql"
vercel env add DB_HOST production <<< "$DB_HOST"
vercel env add DB_PORT production <<< "$DB_PORT"
vercel env add DB_DATABASE production <<< "$DB_DATABASE"
vercel env add DB_USERNAME production <<< "$DB_USERNAME"
vercel env add DB_PASSWORD production <<< "$DB_PASSWORD"
vercel env add DB_SSLMODE production <<< "require"
vercel env add APP_ENV production <<< "production"
vercel env add APP_DEBUG production <<< "false"
vercel env add LOG_CHANNEL production <<< "stderr"

echo ""
echo "✅ Environment variables berhasil di-set!"
echo ""

read -p "🔄 Redeploy sekarang? (y/n): " DEPLOY
if [[ $DEPLOY == "y" || $DEPLOY == "Y" ]]; then
    echo ""
    echo "🚀 Deploying to production..."
    vercel --prod
    echo ""
    echo "✅ Deployment selesai!"
else
    echo ""
    echo "⚠️  Jangan lupa run: vercel --prod"
fi

echo ""
echo "🎉 Setup selesai!"
echo "Cek: https://reservasi-camping.vercel.app"
