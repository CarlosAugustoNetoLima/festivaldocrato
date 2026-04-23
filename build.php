<?php
/**
 * Script de build estático para GitHub Pages
 */

$base    = '/festivaldocrato';
$distDir = __DIR__ . '/dist';
$pubDir  = __DIR__ . '/public';

$routes = [
    '/'            => '',
    '/bilheteira'  => '/bilheteira',
    '/lineup'      => '/lineup',
    '/artistas'    => '/artistas',
    '/campismo'    => '/campismo',
    '/loja'        => '/loja',
    '/noticias'    => '/noticias',
    '/sobre'       => '/sobre',
    '/info'        => '/info',
    '/como-chegar' => '/como-chegar',
    '/contactos'   => '/contactos',
];

foreach ($routes as $uri => $outPath) {
    $_SERVER['HTTP_HOST']   = 'carlosaugustonetonelima.github.io';
    $_SERVER['REQUEST_URI'] = $uri;

    ob_start();
    chdir($pubDir);
    include $pubDir . '/index.php';
    $html = ob_get_clean();

    // Corrigir paths absolutos para o subdiretório do GitHub Pages
    $html = str_replace('href="/assets/',  "href=\"$base/assets/",  $html);
    $html = str_replace('src="/assets/',   "src=\"$base/assets/",   $html);
    $html = str_replace("href=\"/\""    ,  "href=\"$base/\"",       $html);
    $html = preg_replace('#href="/(bilheteira|lineup|artistas|campismo|loja|noticias|sobre|info|como-chegar|contactos|produto|pesquisa)"#',
        "href=\"$base/$1\"", $html);

    $outDir = $distDir . $outPath;
    if (!is_dir($outDir)) mkdir($outDir, 0755, true);

    file_put_contents($outDir . '/index.html', $html);
    echo "Gerado: $outDir/index.html\n";
}

// Copiar assets (rsync garante que não aninha assets/assets/)
echo "Copiando assets...\n";
shell_exec("rsync -a --delete $pubDir/assets/ $distDir/assets/");
echo "Build concluído!\n";
