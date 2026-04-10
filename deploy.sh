#!/bin/bash
set -e

echo "Preparando para gerar o site estático..."

# Verifica se o servidor local está rodando na porta 8000
if ! lsof -i :8000 > /dev/null; then
    echo "Erro: O servidor PHP (localhost:8000) não está rodando. Inicie com 'php -S localhost:8000 -t public' primeiro."
    exit 1
fi

mkdir -p _site
ROUTES=("" "bilheteira" "sobre" "como-chegar" "campismo" "o-que-fazer" "contactos" "noticias" "pesquisa" "artistas" "bilhetes" "info" "lineup")

for route in "${ROUTES[@]}"; do
    if [ -z "$route" ]; then
        NAME="index"
        URL="http://localhost:8000/"
    else
        NAME="$route"
        URL="http://localhost:8000/$route"
    fi
    echo "Exportando: $URL -> _site/$NAME.html"
    curl -s "$URL" > "_site/$NAME.html"
    
    # Substituir URLs absolutas por relativas para o GitHub Pages
    sed -i '' 's|href="/bilheteira"|href="bilheteira.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/sobre"|href="sobre.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/como-chegar"|href="como-chegar.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/campismo"|href="campismo.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/o-que-fazer"|href="o-que-fazer.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/contactos"|href="contactos.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/noticias"|href="noticias.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/pesquisa"|href="pesquisa.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/artistas"|href="artistas.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/bilhetes"|href="bilhetes.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/info"|href="info.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/lineup"|href="lineup.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/"|href="index.html"|g' "_site/$NAME.html"
    
    # Ajustes para parâmetros de pesquisa e sub-menus de âncora
    sed -i '' 's|action="/pesquisa"|action="pesquisa.html"|g' "_site/$NAME.html"
    sed -i '' 's|href="/sobre#|href="sobre.html#|g' "_site/$NAME.html"
    sed -i '' 's|href="/contactos#|href="contactos.html#|g' "_site/$NAME.html"
    
    sed -i '' 's|href="/assets/|href="assets/|g' "_site/$NAME.html"
    sed -i '' 's|src="/assets/|src="assets/|g' "_site/$NAME.html"
    sed -i '' 's|href="/themes/|href="themes/|g' "_site/$NAME.html"
    sed -i '' 's|src="/themes/|src="themes/|g' "_site/$NAME.html"
done

echo "Copiando assets estáticos..."
cp -R public/assets _site/
cp -R public/themes _site/

# Remover arquivos PHP na cópia dos temas
find _site/themes -name "*.php" -type f -delete

echo "Atualizando a branch gh-pages..."

CURRENT_BRANCH=$(git rev-parse --abbrev-ref HEAD)

# Faz checkout para gh-pages, limpando o working directory
git checkout gh-pages

# Substitui o conteúdo antigo pelo novo (_site/* -> ./)
cp -R _site/* ./
touch .nojekyll

# Limpa diretório _site
rm -rf _site

echo "Comitando as mudanças e dando push..."
git add .
git commit -m "deploy: versão estática para GitHub Pages ($(date '+%Y-%m-%d %H:%M:%S'))" || true
git push origin gh-pages

# Volta para a branch original
git checkout $CURRENT_BRANCH

echo "✅ Deploy concluído com sucesso!"
