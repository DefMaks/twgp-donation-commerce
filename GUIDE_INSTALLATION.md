# 📦 Guide d'Installation - TwigaPaie Commerce & Donation

## 🎯 Résumé du Plugin

**TwigaPaie - Commerce & Donation** est un plugin WordPress complet qui permet :
- ✅ Accepter des **donations** avec campagnes personnalisables
- ✅ Vendre des **contenus numériques** (e-commerce)
- ✅ Paiements via **E-Money** (Orange, Vodacom, Airtel, Africell)
- ✅ Paiements via **E-Card** (cartes bancaires)
- ✅ Enregistrement automatique dans **Supabase**
- ✅ Calcul automatique des **frais** (6% : 2,5% + 3,5%)
- ✅ Formatage intelligent des **numéros RDC**

---

## 📁 Fichiers livrés

```
/app/
├── twigapaie-commerce-donation/          # Dossier du plugin
│   ├── twigapaie-commerce-donation.php   # Fichier principal
│   ├── includes/                          # 9 classes PHP
│   ├── admin/                             # Interface admin
│   ├── public/                            # Templates frontend
│   ├── assets/images/logo.png             # Logo TwigaPaie
│   └── README.md                          # Documentation complète
│
├── twigapaie-commerce-donation.zip       # 📦 FICHIER ZIP INSTALLABLE
├── README.md                              # Documentation projet
└── GUIDE_INSTALLATION.md                 # Ce fichier

```

---

## 🚀 Installation du Plugin

### Méthode 1 : Via l'interface WordPress (Recommandé)

1. **Télécharger le fichier ZIP**
   - Fichier : `/app/twigapaie-commerce-donation.zip` (453 KB)

2. **Installer dans WordPress**
   - Connexion à l'admin WordPress
   - Aller dans **Extensions > Ajouter**
   - Cliquer sur **Téléverser une extension**
   - Sélectionner `twigapaie-commerce-donation.zip`
   - Cliquer sur **Installer maintenant**
   - Cliquer sur **Activer**

3. **Vérifier l'installation**
   - Un nouveau menu **TwigaPaie** apparaît dans la sidebar
   - Avec les sous-menus : Tableau de bord, Transactions, Campagnes, Produits, Paramètres

### Méthode 2 : Via FTP

1. **Décompresser le ZIP**
   ```bash
   unzip twigapaie-commerce-donation.zip
   ```

2. **Upload via FTP**
   - Se connecter au serveur FTP
   - Naviguer vers `/wp-content/plugins/`
   - Uploader le dossier `twigapaie-commerce-donation/`

3. **Activer le plugin**
   - Aller dans **Extensions**
   - Activer "TwigaPaie - Commerce & Donation"

---

## ⚙️ Configuration Obligatoire

### Étape 1 : Configuration TwigaPaie API

1. Aller dans **TwigaPaie > Paramètres**

2. **Section : Configuration API TwigaPaie**
   ```
   Clé API TwigaPaie: e50a2ac295a93b465266ae176ba462c272a3072eff7cea910219cccf88e716c6
   Mode test: ☑ Activer (pour les tests)
   ```

3. **Section : Configuration des frais**
   ```
   Devise par défaut: CDF (ou USD)
   Frais agrégateur: 2.5 %
   Frais DefMaks: 3.5 %
   Total des frais: 6.0 %
   ```

4. Cliquer sur **Enregistrer les paramètres**

### Étape 2 : Configuration Supabase

1. Toujours dans **TwigaPaie > Paramètres**

2. **Section : Configuration Supabase**
   ```
   URL Supabase: https://hcpogyjdbtcxndzpyjvd.supabase.co
   
   Clé Anon Supabase:
   eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImhjcG9neWpkYnRjeG5kenB5anZkIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTI4ODc2NjIsImV4cCI6MjA2ODQ2MzY2Mn0.Y-V4hPt_c1rl2ffYZ9nG53R4VuhzrmBIseJSlqJvaNo
   ```

3. Cliquer sur **Enregistrer les paramètres**

---

## 🧪 Tests du Plugin

### Test 1 : Créer une campagne de donation

1. Aller dans **TwigaPaie > Campagnes**
2. Cliquer sur **Ajouter une campagne**
3. Remplir :
   - **Titre :** Aidez-nous à construire une école
   - **Description :** Votre don aidera...
   - **Objectif :** 100000 CDF
   - **Montants prédéfinis :** 1000,5000,10000,25000
4. Ajouter une image à la une
5. **Publier**

### Test 2 : Afficher le formulaire de donation

1. Créer une nouvelle **Page** : "Faire un don"
2. Ajouter le shortcode :
   ```
   [twigapaie_donation_form campaign_id="1"]
   ```
   (Remplacer `1` par l'ID de votre campagne)
3. **Publier** la page
4. Visiter la page pour voir le formulaire

### Test 3 : Créer un produit

1. Aller dans **TwigaPaie > Produits**
2. Cliquer sur **Ajouter un produit**
3. Remplir :
   - **Titre :** Ebook WordPress
   - **Description :** Guide complet...
   - **Prix CDF :** 5000
   - **Prix USD :** 5
   - **URL du fichier :** (télécharger un PDF)
4. Ajouter une image à la une
5. **Publier**

### Test 4 : Créer les pages e-commerce

1. **Page "Boutique"**
   ```
   [twigapaie_products limit="12"]
   ```

2. **Page "Panier"**
   ```
   [twigapaie_cart]
   ```

3. **Page "Paiement"**
   ```
   [twigapaie_checkout]
   ```

### Test 5 : Simuler un paiement

1. Aller sur la page de donation
2. Remplir le formulaire :
   - **Nom :** Jean Dupont
   - **Email :** jean@example.com
   - **Téléphone :** 0822032855
   - **Montant :** 5000 CDF
   - **Méthode :** Mobile Money
3. Cliquer sur **Faire un don**
4. Le plugin détectera automatiquement Vodacom M-Pesa

### Test 6 : Vérifier le tableau de bord

1. Aller dans **TwigaPaie > Tableau de bord**
2. Voir les statistiques :
   - Donations complétées
   - Total donations
   - Commandes complétées
   - Total ventes

### Test 7 : Consulter les transactions

1. Aller dans **TwigaPaie > Transactions**
2. Filtrer par :
   - Type (Donations / Commandes)
   - Statut (Pending / Processing / Completed / Failed)
3. Voir les détails de chaque transaction

---

## 📱 Formatage des numéros de téléphone

Le plugin détecte et formate automatiquement les numéros RDC :

| Opérateur | Préfixes | Exemple saisi | Formaté | Provider ID |
|-----------|----------|---------------|---------|-------------|
| **Orange Money** | 80, 84, 85, 89 | 0850000000 | 0850000000 | 10 |
| **Vodacom M-Pesa** | 81, 82, 83 | 0822032855 | 243822032855 | 9 |
| **Airtel Money** | 97, 98, 99 | 0990000000 | 990000000 | 17 |
| **Africell Money** | 90 | 0900000000 | 0900000000 | 19 |

**Détection automatique :** Le plugin analyse les 2 premiers chiffres pour identifier l'opérateur.

---

## 🔍 Vérification Supabase

Après un paiement réussi, vérifiez dans Supabase :

### Table `transactions`

Colonnes importantes :
- `amount` : Montant de la transaction
- `currency` : CDF ou USD
- `transaction_type` : donation ou purchase
- `defmaks_revenue_cdf` : Revenus DefMaks en CDF (3,5% du montant)
- `defmaks_revenue_usd` : Revenus DefMaks en USD (3,5% du montant)
- `external_reference` : ID de commande TwigaPaie

**Exemple de calcul :**
```
Montant transaction : 10000 CDF
Frais total (6%) : 600 CDF
  - Agrégateur (2,5%) : 250 CDF
  - DefMaks (3,5%) : 350 CDF
Net client : 9400 CDF
```

### Tables connexes

- `profiles` : Profil de l'utilisateur créé automatiquement
- `wallets` : Wallet associé au profil
- `clients` : Information du client (UUID + initiales)

---

## 🎨 Personnalisation du design

Les styles sont modifiables dans :
- **Admin :** `/admin/css/admin-style.css`
- **Public :** `/public/css/public-style.css`

Variables CSS disponibles :
```css
:root {
    --twigapaie-primary: #2271b1;
    --twigapaie-secondary: #646970;
    --twigapaie-success: #00a32a;
    --twigapaie-error: #d63638;
}
```

---

## 📊 Webhooks TwigaPaie

Le plugin expose un endpoint webhook automatique :

```
https://votre-site.com/?twigapaie_webhook=1
```

**Utilisé pour :**
- Confirmation des paiements E-Money
- Confirmation des paiements E-Card
- Mise à jour du statut des transactions
- Enregistrement dans Supabase

**Configuration dans TwigaPaie :**
Donnez cette URL à votre gestionnaire TwigaPaie pour recevoir les notifications de paiement.

---

## 🛠️ Dépannage

### Le menu TwigaPaie n'apparaît pas
- Vérifier que le plugin est bien activé
- Vérifier les permissions utilisateur (besoin de `manage_options`)

### Les paiements ne fonctionnent pas
- Vérifier la clé API TwigaPaie dans les paramètres
- Activer le mode test pour déboguer
- Consulter les logs : `/wp-content/debug.log`

### Les transactions ne s'enregistrent pas dans Supabase
- Vérifier l'URL et la clé Supabase
- Vérifier que les tables existent dans Supabase
- Consulter les logs d'erreur PHP

### Erreur de formatage de numéro
- Vérifier que le numéro commence par 0 ou 243
- Le numéro doit avoir 9 ou 10 chiffres
- Seuls les numéros RDC sont supportés

---

## 📞 Support

**Site web :** https://defmaks.com  
**Email :** support@defmaks.com

---

## ✅ Checklist de déploiement

- [ ] Plugin installé et activé
- [ ] Clé API TwigaPaie configurée
- [ ] Supabase configuré (URL + Clé)
- [ ] Frais configurés (2,5% + 3,5%)
- [ ] Campagne de test créée
- [ ] Produit de test créé
- [ ] Pages créées (Boutique, Panier, Paiement)
- [ ] Test de donation effectué
- [ ] Test d'achat effectué
- [ ] Transaction vérifiée dans Supabase
- [ ] Webhook configuré dans TwigaPaie

---

## 🎉 Félicitations !

Votre plugin **TwigaPaie - Commerce & Donation** est maintenant opérationnel !

Vous pouvez maintenant :
- Accepter des donations pour vos campagnes
- Vendre des contenus numériques
- Recevoir des paiements via Mobile Money et cartes bancaires
- Gérer automatiquement le partage des revenus via Supabase

**Bon lancement ! 🚀**
