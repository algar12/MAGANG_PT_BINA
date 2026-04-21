#!/bin/bash
# Script untuk menjalankan semua service Smart Timbangan
PROJECT="$HOME/smart-timbangan"

echo "🚀 Menjalankan Smart Timbangan System..."
echo ""

# Pastikan MySQL berjalan
sudo systemctl start mysql 2>/dev/null
echo "✅ MySQL: berjalan"

# Jalankan AI Server di background
bash "$PROJECT/ai-server/start.sh" &
AI_PID=$!
echo "✅ AI Server: PID $AI_PID (port 5000)"

# Jalankan Laravel di background
cd "$PROJECT/backend"
php artisan serve --host=0.0.0.0 --port=8000 &
LARAVEL_PID=$!
echo "✅ Laravel: PID $LARAVEL_PID (port 8000)"

# Jalankan n8n di background
n8n start &
N8N_PID=$!
echo "✅ n8n: PID $N8N_PID (port 5678)"

echo ""
echo "════════════════════════════════════"
echo "  Semua service berjalan!"
echo "  AI Server : http://localhost:5000"
echo "  Laravel   : http://localhost:8000"
echo "  n8n       : http://localhost:5678"
echo "════════════════════════════════════"
echo ""
echo "Tekan Ctrl+C untuk menghentikan semua..."

# Tunggu dan bersihkan saat keluar
trap "kill $AI_PID $LARAVEL_PID $N8N_PID 2>/dev/null; echo 'Semua service dihentikan'" EXIT
wait
