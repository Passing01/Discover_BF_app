# Déploiement sur Render

Ce guide explique comment déployer l'application Laravel sur Render.

## Prérequis

- Un compte Render (https://render.com/)
- Un dépôt Git (GitHub, GitLab ou Bitbucket) lié à votre projet
- Un service de base de données PostgreSQL (fourni par Render)

## Étapes de déploiement

### 1. Configuration du dépôt Git

Assurez-vous que votre projet est poussé sur un dépôt Git (GitHub, GitLab ou Bitbucket).

### 2. Configuration sur Render

1. Connectez-vous à votre compte Render
2. Cliquez sur "New" puis sélectionnez "Web Service"
3. Liez votre dépôt Git
4. Configurez le service avec les paramètres suivants :
   - **Name** : discover-bf (ou le nom de votre choix)
   - **Region** : Sélectionnez la région la plus proche de vos utilisateurs
   - **Branch** : main (ou votre branche de déploiement)
   - **Runtime** : Docker
   - **Build Command** : `docker build -t discover-bf .`
   - **Start Command** : Voir la section "Start Command" ci-dessous
   - **Plan** : Free (ou un plan payant pour les applications de production)

### 3. Variables d'environnement

Configurez les variables d'environnement suivantes dans l'onglet "Environment" de votre service Render :

```
APP_NAME=Discover_BF
APP_ENV=production
APP_DEBUG=false
APP_KEY=Générez une clé avec: `php artisan key:generate --show`
APP_URL=https://votre-app.onrender.com

# Base de données (sera automatiquement configuré par Render)
DB_CONNECTION=pgsql

# Session
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.onrender.com

# Cache
CACHE_DRIVER=file
QUEUE_CONNECTION=database

# Autres paramètres
LOG_CHANNEL=stderr
```

### 4. Base de données PostgreSQL

1. Dans le tableau de bord Render, cliquez sur "New" puis sélectionnez "PostgreSQL"
2. Configurez la base de données :
   - **Name** : discover-bf-db
   - **Database** : discover_bf
   - **User** : discover_bf_user
   - **Plan** : Free (ou un plan payant pour la production)
3. Une fois créée, allez dans l'onglet "Environment" de votre service web et ajoutez les variables d'environnement liées à la base de données (générées automatiquement par Render)

### 5. Commandes de démarrage personnalisées

Dans la section "Advanced" de la configuration de votre service, ajoutez la commande de démarrage suivante :

```bash
# Vérifier si le dossier build existe, sinon le créer
if [ ! -d "public/build" ]; then
  mkdir -p public/build
fi

# Exécuter les migrations et optimiser
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
php artisan view:cache
php artisan event:cache

# Démarrer le serveur
docker run -p 10000:10000 \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e APP_URL=https://${RENDER_EXTERNAL_HOSTNAME} \
  -e VITE_APP_URL=https://${RENDER_EXTERNAL_HOSTNAME} \
  -e MIX_ASSET_URL=https://${RENDER_EXTERNAL_HOSTNAME} \
  -e FORCE_HTTPS=true \
  discover-bf
```

### 6. Déploiement continu

Le déploiement continu est activé par défaut. Chaque push sur la branche configurée déclenchera un nouveau déploiement.

## Dépannage

### Les migrations échouent

Si les migrations échouent, vérifiez que :
1. Les variables d'environnement de la base de données sont correctement configurées
2. L'utilisateur de la base de données a les bonnes permissions
3. La base de données est accessible depuis votre service web

### Les assets ne se chargent pas

Si les fichiers CSS/JS ne se chargent pas :
1. Vérifiez que la commande `npm run build` s'exécute correctement pendant le déploiement
2. Assurez-vous que `VITE_APP_URL` est correctement défini
3. Vérifiez les permissions des dossiers `public/build` et `storage`

### Journalisation

Les logs de l'application sont disponibles dans l'onglet "Logs" de votre service Render.

## Mise à jour de l'application

Pour mettre à jour l'application :
1. Poussez vos modifications sur la branche de déploiement
2. Un nouveau déploiement sera automatiquement déclenché
3. Si nécessaire, exécutez manuellement les migrations :
   ```bash
   php artisan migrate --force
   ```

## Sécurité

- Ne stockez jamais de clés secrètes ou d'informations sensibles dans votre dépôt Git
- Utilisez les variables d'environnement pour la configuration sensible
- Activez le protocole HTTPS dans les paramètres de votre service Render
- Mettez régulièrement à jour vos dépendances pour corriger les failles de sécurité

## Support

Pour toute question ou problème, consultez la documentation de Render ou contactez le support :
- [Documentation Render](https://render.com/docs)
- [Support Render](https://render.com/contact)
