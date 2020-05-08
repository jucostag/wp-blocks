echo 'Clonando dependência angular-lazy-img'
git clone --branch v1.2.2 git://github.com/Pentiado/angular-lazy-img.git

echo 'Apagando dependência, se já existir no diretório vendor'
rm -rf assets/scripts/vendor/angular-lazy-img/.git
rm -rf assets/scripts/vendor/angular-lazy-img

echo 'Movendo dependência para o diretório correto'
mv angular-lazy-img assets/scripts/vendor/