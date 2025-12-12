# TwigaPaie - Commerce & Donation

**Version:** 1.0.0  
**Auteur:** DefMaks  
**Site web:** [https://defmaks.com](https://defmaks.com)  
**Licence:** GPL-3.0+

## Description

Plugin WordPress complet pour accepter des donations et vendre des contenus numériques avec **TwigaPaie** — la passerelle de paiement africaine qui supporte Orange Money, Airtel Money, M-Pesa (Vodacom) et Africell Money, ainsi que les paiements par carte bancaire.

Toutes les transactions sont automatiquement enregistrées dans **Supabase** pour une répartition transparente des revenus entre créateurs et la plateforme.

### 🎯 Fonctionnalités principales

#### 💝 Système de Donations
- Formulaires de donation personnalisables
- Campagnes de donation avec objectifs
- Montants prédéfinis et montants personnalisés
- Historique des donateurs

#### 🛍️ Système E-Commerce
- Vente de contenus numériques (PDF, vidéos, audio, etc.)
- Gestion complète des produits
- Panier d'achat
- Processus de checkout sécurisé
- Téléchargement automatique après paiement

#### 💳 Méthodes de Paiement
- **E-Money (Mobile Money)** : Orange Money, Vodacom M-Pesa, Airtel Money, Africell Money
- **E-Card (Cartes bancaires)** : Visa, Mastercard via FlexPay
- Formatage intelligent des numéros de téléphone RDC

#### 📊 Intégration Supabase
- Enregistrement automatique de toutes les transactions
- Calcul des frais : **6% total** (2,5% agrégateur + 3,5% DefMaks)
- Gestion des profils utilisateurs et wallets
- Devises supportées : **CDF** et **USD**

#### ⚙️ Interface d'Administration
- Tableau de bord avec statistiques
- Gestion des campagnes et produits
- Historique complet des transactions
- Configuration TwigaPaie et Supabase
- Filtrage et recherche avancés

---

## 🛠️ Installation

### Méthode 1 : Installation via l'interface WordPress

1. Téléchargez le fichier ZIP du plugin
2. Allez dans **Extensions > Ajouter**
3. Cliquez sur **Téléverser une extension**
4. Sélectionnez le fichier ZIP
5. Cliquez sur **Installer maintenant**
6. Activez le plugin

### Méthode 2 : Installation manuelle via FTP

1. Décompressez le fichier ZIP
2. Uploadez le dossier `twigapaie-commerce-donation` dans `/wp-content/plugins/`
3. Allez dans **Extensions** et activez le plugin

---

## ⚙️ Configuration

### 1. Configuration TwigaPaie

1. Allez dans **TwigaPaie > Paramètres**
2. Entrez votre **Clé API TwigaPaie** (format Authorization Bearer)
   ```
   e50a2ac295a93b465266ae176ba462c272a3072eff7cea910219cccf88e716c6
   ```
3. Choisissez la devise par défaut (**CDF** ou **USD**)
4. Configurez les frais :
   - Frais agrégateur : **2,5%**
   - Frais DefMaks : **3,5%**
   - Total : **6%**

### 2. Configuration Supabase

1. Dans **TwigaPaie > Paramètres**, section **Configuration Supabase**
2. Entrez l'**URL Supabase** :
   ```
   https://hcpogyjdbtcxndzpyjvd.supabase.co
   ```
3. Entrez la **Clé Anon Supabase** :
   ```
   eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImhjcG9neWpkYnRjeG5kenB5anZkIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTI4ODc2NjIsImV4cCI6MjA2ODQ2MzY2Mn0.Y-V4hPt_c1rl2ffYZ9nG53R4VuhzrmBIseJSlqJvaNo
   ```

### 3. Schéma de base de données Supabase

Le plugin utilise les tables suivantes dans Supabase :
- `profiles` : Profils utilisateurs
- `wallets` : Portefeuilles des utilisateurs
- `transactions` : Historique des transactions
- `clients` : Informations des clients

Les transactions sont enregistrées avec :
- **defmaks_revenue_cdf** : Revenus DefMaks en CDF
- **defmaks_revenue_usd** : Revenus DefMaks en USD
- Calcul automatique des frais (6% total)

---

## 📝 Utilisation

### Créer une campagne de donation

1. Allez dans **TwigaPaie > Campagnes**
2. Cliquez sur **Ajouter une campagne**
3. Remplissez les informations :
   - Titre de la campagne
   - Description
   - Objectif financier
   - Devise (CDF ou USD)
   - Montants prédéfinis (ex: 1000,5000,10000)
   - Date de fin (optionnel)
4. Ajoutez une image à la une
5. Publiez la campagne

**Afficher le formulaire de donation :**
```
[twigapaie_donation_form campaign_id="123"]
```

### Créer un produit

1. Allez dans **TwigaPaie > Produits**
2. Cliquez sur **Ajouter un produit**
3. Remplissez les informations :
   - Titre du produit
   - Description
   - Prix en CDF
   - Prix en USD
   - URL du fichier (ou téléchargez un fichier)
   - Limite de téléchargement
4. Ajoutez une image à la une
5. Publiez le produit

**Afficher les produits :**
```
[twigapaie_products limit="12"]
```

### Créer les pages essentielles

#### Page Panier
Créez une page "Panier" et ajoutez le shortcode :
```
[twigapaie_cart]
```

#### Page Checkout
Créez une page "Paiement" et ajoutez le shortcode :
```
[twigapaie_checkout]
```

---

## 📱 Formatage des numéros de téléphone (RDC)

Le plugin formate automatiquement les numéros selon l'opérateur détecté :

| Opérateur | Préfixes | Format attendu | Exemple |
|-----------|----------|----------------|----------|
| **Orange Money** | 80, 84, 85, 89 | 0XXXXXXXXX | 0850000000 |
| **Vodacom M-Pesa** | 81, 82, 83 | 243XXXXXXXXX | 243810000000 |
| **Airtel Money** | 97, 98, 99 | XXXXXXXXX | 990000000 |
| **Africell Money** | 90 | 0XXXXXXXXX | 0900000000 |

Le plugin détecte automatiquement le fournisseur et formate le numéro correctement.

---

## 🔧 Shortcodes disponibles

### Formulaire de donation
```
[twigapaie_donation_form campaign_id="123"]
```
**Paramètres :**
- `campaign_id` : ID de la campagne (optionnel, 0 pour donation générale)

### Grille de produits
```
[twigapaie_products limit="12"]
```
**Paramètres :**
- `limit` : Nombre de produits à afficher (défaut: 12)

### Panier d'achat
```
[twigapaie_cart]
```

### Page de paiement
```
[twigapaie_checkout]
```

---

## 📊 Tableau de bord

Accédez au tableau de bord dans **TwigaPaie > Tableau de bord** pour voir :

- Nombre total de donations complétées
- Montant total des donations
- Nombre total de commandes
- Montant total des ventes
- Dernières transactions
- Statistiques en temps réel

---

## 🔍 Webhooks

Le plugin gère automatiquement les webhooks TwigaPaie via l'endpoint :
```
https://votre-site.com/?twigapaie_webhook=1
```

Les webhooks sont utilisés pour :
- Confirmer les paiements E-Money
- Confirmer les paiements par carte
- Enregistrer les transactions dans Supabase
- Envoyer les emails de confirmation

---

## ❓ FAQ

### Comment obtenir une clé API TwigaPaie ?
Contactez l'administrateur TwigaPaie pour obtenir votre clé API.

### Quelles devises sont supportées ?
Actuellement : **CDF** (Franc Congolais) et **USD** (Dollar Américain).

### Les paiements sont-ils sécurisés ?
Oui, tous les paiements sont traités via l'API sécurisée de TwigaPaie.

### Comment les frais sont-ils calculés ?
- 2,5% pour l'agrégateur de paiement
- 3,5% pour DefMaks
- **Total : 6%** sur chaque transaction

### Puis-je modifier les frais ?
Oui, dans **TwigaPaie > Paramètres > Configuration des frais**.

### Les transactions sont-elles enregistrées localement ?
Oui, dans WordPress ET dans Supabase pour la répartition des revenus.

---

## 👥 Support

Pour toute question ou problème :
- Site web : [https://defmaks.com](https://defmaks.com)
- Email : support@defmaks.com

---

## 📝 Changelog

### Version 1.0.0 (2025)
- Lancement initial du plugin
- Système de donations complet
- Système e-commerce pour contenus numériques
- Intégration TwigaPaie (E-Money + E-Card)
- Intégration Supabase
- Formatage intelligent des numéros RDC
- Interface d'administration complète
- Dashboard avec statistiques
- Support CDF et USD

---

## 📜 Licence

Ce plugin est distribué sous licence **GPL-3.0+**.  
Vous êtes libre de l'utiliser, le modifier et le redistribuer selon les termes de cette licence.

---

**Développé avec ❤️ par DefMaks**
