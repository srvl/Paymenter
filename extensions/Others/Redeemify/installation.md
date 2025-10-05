=======================================
Redeemify – Advanced Promo Code Extension for Paymenter
=======================================

🇺🇸 INSTALLATION (ENGLISH VERSION)

1. Upload the "Redeemify" folder to the `extensions/Others` directory on your Paymenter server.
2. Login to your Paymenter admin panel.
3. Go to "Extensions" menu and activate "Redeemify".
4. After activation, you will see a new menu under "Redeemify" > "Redeemify Codes".
5. Click "Create" to add a new redeem code.
6. Fill in the credit amount, currency, expiration date, and max usage.
7. (Optional) Use the "Conditions" section to define who can redeem:
   - You can set rules based on user ID, country, account age, order count, etc.
   - Use AND/OR logic by grouping rules.
   - Use NOT logic by toggling "negate" on condition group.
8. Save the code.

💡 Discord Webhook (Optional)
-----------------------------
To enable Discord notifications when a new code is created:

1. Go to the "Extension Config" panel for Redeemify.
2. Set:
   - `use_discord_webhook` = true
   - `discord_webhook` = your Discord webhook URL
   - `to_ping_role_id` = optional role ID to ping (e.g. 123456789)

When a new redeem code is created, it will send a rich embed to the specified Discord channel.

📦 Done! You're ready to roll out codes 🚀
