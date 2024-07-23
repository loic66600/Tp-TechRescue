

# 🛠 Projet Réparation Informatique - Symfony avec Docker

Ce projet utilise Symfony et Docker pour créer une application de gestion de réparation informatique robuste et portable.

## 🚀 Démarrage rapide

### Prérequis

- Docker
- Docker Compose

### Installation initiale

1. Naviguez vers le dossier `RUNNER`.
2. Ouvrez un terminal et exécutez les commandes suivantes pour donner les permissions d'exécution aux scripts :
   ```bash
   chmod +x setup.sh
   chmod +x automate_setup.sh
   ```
3. Cliquez droit sur `automate_setup.sh` -> "Exécuter comme un programme".
4. Dans le terminal, exécutez :
   ```bash
   ./automate_setup.sh
   ```

### Après le lancement des conteneurs Docker

Exécutez les commandes suivantes :

```bash
# Installer les dépendances Composer
docker exec -it phpimmo composer install

# Installer les dépendances Yarn
docker exec -it nodeimmo yarn

# Compiler les assets
docker exec -it nodeimmo yarn encore dev

# Compiler les assets en mode watch
docker exec -it nodeimmo yarn encore dev --watch

# Installer AOS (Animate On Scroll)
docker exec -it nodeimmo yarn add aos

# Installer le notifier Symfony
docker exec -it phpimmo composer require symfony/notifier

# Installer Faker pour les données de test
docker exec -it phpimmo composer require fakerphp/faker --dev

# Installer Stripe pour les paiements
docker exec -it phpimmo composer require stripe/stripe-php
```

## 🐳 Commandes Docker courantes

```bash
# Lancer Docker Compose
docker-compose up --build

# Lancer en mode détaché
docker-compose up --build -d

# Arrêter Docker Compose
docker-compose down

# Redémarrer Docker Compose
docker-compose restart
```

## 🛠 Commandes Symfony utiles

```bash
# Créer un nouveau projet Symfony (dans un dossier app vide)
docker exec -it [nom_du_container_php] composer create-project symfony/skeleton ./

# Ajouter un bundle
docker exec -it [nom_du_container_php] composer req [nom_du_bundle]

# Supprimer un bundle
docker exec -it [nom_du_container_php] composer remove [nom_du_bundle]

# Voir les commandes disponibles
docker exec -it [nom_du_container_php] php bin/console

# Nettoyer le cache
docker exec -it [nom_du_container_php] php bin/console cache:clear

# Afficher les routes
docker exec -it [nom_du_container_php] php bin/console debug:router
```

## 🔧 Gestion des droits

```bash
# Forcer les droits utilisateur (dans le répertoire principal)
sudo chown -R [nomUtilisateur ou uid]:[nom_du_groupe ou gid] app/

# Changer les droits dans le container
docker exec -it [nom_du_container_php] sh
chown -R www-data:www-data ./

# Si le problème persiste
chmod -R 755 ./
```

## 📦 Installation de packages supplémentaires

```bash
# Twig
docker exec -it [nom_du_container_php] composer req twig

# Maker Bundle (dev)
docker exec -it [nom_du_container_php] composer req --dev symfony/maker-bundle

# Asset
docker exec -it [nom_du_container_php] composer req symfony/asset

# Doctrine Fixtures (dev)
docker exec -it [nom_du_container_php] composer req --dev doctrine/doctrine-fixtures-bundle

# Debug Bar (dev)
docker exec -it [nom_du_container_php] composer req --dev symfony/profiler-pack

# Form, Validator et CSRF
docker exec -it [nom_du_container_php] composer req symfony/form validator symfony/security-csrf
```

## 📝 Notes

- Remplacez `[nom_du_container_php]` par le nom réel de votre conteneur PHP dans Docker.
- Assurez-vous d'avoir les permissions nécessaires pour exécuter les commandes Docker et Symfony.

## 🤝 Contribution

Les contributions à ce projet sont les bienvenues. N'hésitez pas à ouvrir une issue ou à soumettre une pull request.

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

---

Ce README offre une structure claire et informative pour votre projet de réparation informatique avec Symfony et Docker, mettant en avant les commandes essentielles et les étapes d'installation. Les emojis ajoutent une touche visuelle agréable et aident à structurer l'information.