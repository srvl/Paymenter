# 🧩 PaymenterBot Integration Setup

This guide will walk you through installing the **PaymenterBotApi** extension for Paymenter and deploying the companion bot.

---

## 🔧 Step 1: Setup Paymenter API Extension

1. Upload the `PaymenterBotApi` folder to your Paymenter installation under:

   ```
   extensions/Others
   ```

2. Go to your Paymenter **Admin Panel**, then:

   * Navigate to `Extensions`
   * Find and **activate** the `PaymenterBotApi` extension

3. Now, head to `Admin > OAuth Clients` and create a new OAuth client:

   * **Name**: (anything you want)
   * **Redirect URL**:
     This should point to the bot’s hosted endpoint.

     > 📌 Example:
     > If your bot will run on `http://1.1.1.1:3000`, then set redirect URL to:
     > `http://1.1.1.1:3000/link-account`

---

## 🤖 Step 2: Setup PaymenterBot (the bot)

1. Import the provided **Pterodactyl Egg** file `paymenterbot-egg.json` into your Pterodactyl panel

2. Create a new server using the `PaymenterBot` egg

3. Fill in the `.env` file inside the container with the following values:

```env
# Discord
DISCORD_TOKEN=your_discord_bot_token
DISCORD_GUILD_ID=your_discord_server_id

# Web Server (used for OAuth2 redirect endpoint)
HTTP_SERVER=:3000            # or 1.1.1.1:3000 if needed
APP_URL=http://1.1.1.1:3000  # or https if you use SSL

# Paymenter OAuth
PAYMENTER_REDIRECT_URI=http://1.1.1.1:3000/link-account   # must match exactly with the one you set in OAuth
PAYMENTER_ENDPOINT=https://billing.com               # no trailing slash
PAYMENTER_CLIENT_ID=your_oauth_client_id
PAYMENTER_CLIENT_SECRET=your_oauth_client_secret
```

---

## ⚠️ Disclaimer

> 🧠 **PaymenterBot** will automatically sync and cache slash commands on boot.
> Any Discord commands not registered through **PaymenterBot** will be removed on restart.

---
