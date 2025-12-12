# Plugin WordPress TwigaPaie - Commerce & Donation

Ce dépôt contient le plugin WordPress **TwigaPaie - Commerce & Donation** pour accepter des donations et vendre des contenus numériques via TwigaPaie (E-Money et E-Card) avec intégration Supabase.

## 📁 Structure du projet

```
/app/
└── twigapaie-commerce-donation/    # Plugin WordPress complet
    ├── twigapaie-commerce-donation.php    # Fichier principal
    ├── includes/                          # Classes PHP
    ├── admin/                            # Interface admin
    ├── public/                           # Templates publics
    ├── assets/                           # Images et ressources
    ├── languages/                        # Fichiers de traduction
    └── README.md                         # Documentation
```

## 🚀 Installation rapide

### Option 1 : Installation dans WordPress

1. **Compresser le plugin :**
   ```bash
   cd /app
   zip -r twigapaie-commerce-donation.zip twigapaie-commerce-donation/
   ```

2. **Installer dans WordPress :**
   - Téléchargez le fichier ZIP
   - Dans WordPress : Extensions > Ajouter > Téléverser
   - Sélectionnez le ZIP et installez
   - Activez le plugin

### Option 2 : Installation manuelle

1. **Copier le dossier :**
   ```bash
   cp -r /app/twigapaie-commerce-donation /path/to/wordpress/wp-content/plugins/
   ```

2. **Activer le plugin :**
   - Allez dans Extensions
   - Activez "TwigaPaie - Commerce & Donation"

## ⚙️ Configuration initiale

### 1. Paramètres TwigaPaie

Allez dans **TwigaPaie > Paramètres** et configurez :

- **Clé API TwigaPaie :** `e50a2ac295a93b465266ae176ba462c272a3072eff7cea910219cccf88e716c6`
- **Mode test :** Activer pour les tests
- **Devise par défaut :** CDF ou USD

### 2. Paramètres Supabase

- **URL Supabase :** `https://hcpogyjdbtcxndzpyjvd.supabase.co`
- **Clé Anon Supabase :** (voir documentation complète)

### 3. Configuration des frais

- **Frais agrégateur :** 2,5%
- **Frais DefMaks :** 3,5%
- **Total :** 6%

## 📚 Documentation complète

Consultez le fichier `/app/twigapaie-commerce-donation/README.md` pour :

- Guide d'utilisation complet
- Création de campagnes et produits
- Liste des shortcodes disponibles
- Configuration avancée
- FAQ et support

## 👥 Support

- **Site web :** [https://defmaks.com](https://defmaks.com)
- **Email :** support@defmaks.com

## 📝 Licence

GPL-3.0+
