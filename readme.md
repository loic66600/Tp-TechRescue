⚠️ Aprés lancement des container Docker executer les commandes suivantes :
   # Run Composer
    -->   docker exec -it phpimmo composer install

       # Run Yarn
    -->   docker exec -it nodeimmo yarn

       # Run Yarn encore dev
    -->   docker exec -it nodeimmo yarn encore dev

       # Run Yarn encore dev --watch
     -->  docker exec -it nodeimmo yarn encore dev --watch

       # Run Yarn add aos
     --> docker exec -it nodeimmo yarn add aos

      # Run notifier bu
     --> docker exec -it phpimmo composer require symfony/notifier

     !! docker exec -it phpimmo composer require fakerphp/faker --dev


     !! docker exec -it phpimmo composer require stripe/stripe-php
⚠️

# premier lancement de docker compose
docker-compose up --build
# docker compose en mode détaché
docker-compose up --build -d

# arrêt de docker compose en mode détaché
docker-compose down

# redemarrage du docker compose
docker-compose restart

# création du projet symfony
# avoir le docker compose lancé
# attention d'avoir le dossier app vide
docker exec -it [nom du container php] composer create-project symfony/skeleton ./

# ajout de bundle symfony
docker exec -it [nom du container php] composer req [nom du bundle]

# enlever un bundle
docker exec -it [nom du container php] composer remove [nom du bundle]

# voir les commandes de bin/console
docker exec -it [nom du container php] php bin/console

# executer le nettoyage du cache de symfony
docker exec -it [nom du container php] php bin/console cache:clear

# afficher les routes du symfony
docker exec -it [nom du container php] php bin/console debug:router

# forcer les droits pour l'utilisateur
# se placer dans le repertoire principale du projet
sudo chown -R [nomUtilisateur ou uid]:[nom du groupe ou gid] app/
 # changer les droit dans le container
docker exec -it  [nom du container php] sh
# apres faire la commande suivant
chown -R www-data:www-data ./
# si problème persiste
chmod -R 755 ./

# install  twig
docker exec -it [nom du container php] composer req twig

# install  maker bundle
docker exec -it [nom du container php] composer req --dev symfony/maker-bundle

# install  Asset
docker exec -it [nom du container php] composer req symfony/asset

# install Fixture
docker exec -it [nom du container php] composer req --dev doctrine/doctrine-fixtures-bundle

# install Faker
docker exec -it [nom du container php] composer req --dev fzaninotto/faker

# install debug bar
docker exec -it [nom du container php] composer req --dev symfony/profiler-pack

# install form validateur et csrf 
docker exec -it [nom du container php] composer req symfony/profiler-pack validator symfony/security-csrf
