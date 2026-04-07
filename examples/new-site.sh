#!/bin/bash
# Script para criar um novo site a partir do template

SITE_NAME="$1"

if [ -z "$SITE_NAME" ]; then
    echo "Uso: ./new-site.sh nome-do-site"
    exit 1
fi

# Cria estrutura
mkdir -p "../$SITE_NAME"
cp -r ../yanns-starter-template/* "../$SITE_NAME/"
cp -r ../yanns-starter-template/.[^.]* "../$SITE_NAME/" 2>/dev/null

echo "✅ Site '$SITE_NAME' criado!"
echo ""
echo "Próximos passos:"
echo "  cd ../$SITE_NAME"
echo "  editar config/site.php"
echo "  cd public && php -S localhost:8000"
