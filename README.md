# Extension Manager for Pelican Panel

This repository provides a modified version of specific files from the Pelican Panel (version 1.0.0-beta19) to add an Extension Manager feature.

⚠️ **Early Development Stage**  
This project is in an early phase. Features are limited and subject to change. New functionality will be added over time.

## Notes

-   This repository only includes modified files; the full Pelican Panel is **not** redistributed.
    
-   Only tested with version `1.0.0-beta19`.
    
-   Use at your own risk. Test in a non-production environment before deployment.
    
-   Contributions, issues, and pull requests are welcome.

## Compatibility

| Pelican Version         | Status        | Notes                         |
|-------------------------|---------------|-------------------------------|
| 1.0.0-beta19            | ✅ Supported  | Base version for this patch   |
| 1.0.0-beta18 and below  | ❌ Unsupported | Not tested / likely incompatible |
| Future versions         | ❓ Unknown    | Will require adjustments      |


## Extensions

You can test the extension system using the following demo extension:

🔗 [pelican-test-extension](https://github.com/PalmarHealer/pelican-test-extension)

This example is designed to demonstrate basic loading functionality and serves as a template for future extensions.


## Installation

**⚠️ Backup Notice:**  
Before applying this patch, make a backup of your Pelican installation. If you don't have pelican installed yet you can get it from [here](https://pelican.dev/docs/panel/getting-started).

### Making a backup:
If you installed pelican somewhere else please backup that instead
```bash
cp -r /var/www/pelican /var/www/pelican-backup
```
### Navigate to pelican:

```bash
cd /var/www/pelican
```
```bash
curl -L https://github.com/PalmarHealer/pelican-extension-manager/releases/latest/download/panel.tar.gz | sudo tar -xzv
```


### Set correct permissions

Depending on your OS and web server:

-   **NGINX / Apache / Caddy (Debian/Ubuntu):**
    
    ```bash
    sudo chown -R www-data:www-data /var/www/pelican
    
    ```
    
-   **Rocky Linux (NGINX):**
    
    ```bash
    sudo chown -R nginx:nginx /var/www/pelican
    
    ```
    
-   **Rocky Linux (Apache):**
    
    ```bash
    sudo chown -R apache:apache /var/www/pelican
    
    ```
   
