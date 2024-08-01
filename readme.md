```markdown
# 🛠 Projet Réparation Informatique - Symfony avec Docker

Ce projet utilise Symfony et Docker pour créer une application de gestion de réparation informatique robuste et portable.

## 🚀 Démarrage rapide

### Prérequis

- Docker
- Docker Compose

### Installation initiale rapide

1. Naviguez vers le dossier `RUNNER`.
2. Ouvrez un terminal et exécutez les commandes suivantes pour donner les permissions d'exécution aux scripts :
   ```bash
   chmod +x setup.sh
   ```
   ```bash
   chmod +x automate_setup.sh
   ```
3. Cliquez droit sur `automate_setup.sh` -> "Exécuter comme un programme".
4. Dans le terminal, exécutez :
   ```bash
   ./automate_setup.sh
   ```

### Après le lancement des conteneurs Docker initial

Exécutez les commandes suivantes :

```bash
docker exec -it phpimmo composer install
```
```bash
docker exec -it nodeimmo yarn
```
```bash
docker exec -it nodeimmo yarn encore dev
```
```bash
docker exec -it nodeimmo yarn encore dev --watch
```
```bash
docker exec -it nodeimmo yarn add aos
```
```bash
docker exec -it phpimmo composer require symfony/notifier
```
```bash
docker exec -it phpimmo composer require fakerphp/faker --dev
```
```bash
docker exec -it phpimmo composer require stripe/stripe-php
```

## 🐳 Commandes Docker courantes

```bash
docker-compose up --build
```
```bash
docker-compose up --build -d
```
```bash
docker-compose down
```
```bash
docker-compose restart
```

## 🛠 Commandes Symfony utiles

```bash
docker exec -it [nom_du_container_php] composer create-project symfony/skeleton ./
```
```bash
docker exec -it [nom_du_container_php] composer req [nom_du_bundle]
```
```bash
docker exec -it [nom_du_container_php] composer remove [nom_du_bundle]
```
```bash
docker exec -it [nom_du_container_php] php bin/console
```
```bash
docker exec -it [nom_du_container_php] php bin/console cache:clear
```
```bash
docker exec -it [nom_du_container_php] php bin/console debug:router
```

## 🔧 Gestion des droits

```bash
sudo chown -R [nomUtilisateur ou uid]:[nom_du_groupe ou gid] app/
```
```bash
docker exec -it [nom_du_container_php] sh
chown -R www-data:www-data ./
```
```bash
chmod -R 755 ./
```

## 📦 Installation de packages supplémentaires

```bash
docker exec -it [nom_du_container_php] composer req twig
```
```bash
docker exec -it [nom_du_container_php] composer req --dev symfony/maker-bundle
```
```bash
docker exec -it [nom_du_container_php] composer req symfony/asset
```
```bash
docker exec -it [nom_du_container_php] composer req --dev doctrine/doctrine-fixtures-bundle
```
```bash
docker exec -it [nom_du_container_php] composer req --dev symfony/profiler-pack
```
```bash
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
```