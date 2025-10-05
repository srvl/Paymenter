v1.1.0

[api]
- Added endpoint to search users by properties and updated the "me" endpoint with role relation.
- Added request signature verification middleware.
- Added credit management endpoints with signature validation and caching for improved performance.
- Added health check endpoint with status, version, and timestamp.

[bot]
- Added action commands to add/remove credit (supported via slash command or context menu).
- Added redirect UI.
- Added console commands for managing admin access (add, list, and revoke). You can run `help` in the console terminal to see all available commands.
- Added verification signature builder (for secure communication between Paymenter and bot in several endpoints).
- Added API compatibility checking on boot.
