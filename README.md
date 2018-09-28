# Lisk Send File
A tool to upload files, pictures, etc… to the lisk blockchain network.

## Windows
### Download
To use Lisk-Send_File on windows you must download the latest version from the [releases page](https://github.com/S3x0r/Lisk-Send-File/releases).
### Configure
When the download is done you need to configure the `config.php` file with your lisk id and the passphrase to access your lisk id.
```
For example:
/* Lisk address */
$GLOBALS['ADDRESS'] = '8273455169423958419L';
/* address passphrase */
$GLOBALS['PASSWORD'] = 'robust swift grocery peasant forget share enable convince deputy road keep cheap';
```
### Execute
After you’ve configured the config file you just can run the `SEND.BAT` file or go with cmd to the folder that you’ve downloaded and use the following command: `./php/php.exe -c “./php/php.ini” -f ”send.php”`.

### Send
When the uploader is running it will ask you to give a file to send to the blockchain. After you’ve given the file it will start compressing it and splitting it, so it can be sent over the network (this might take some time for bigger files). 
When this is done it will ask for your confirmation to send it. After you pressed yes it will start sending and give you a Data ID when it’s done (you’ll need tis to download the file example of a Data ID: `17700436442799012848`). If you press no it will abort the upload.

Ps. A gui tool is in development.
## Linux

### Pre-configuration
If php and the other extensions aren’t installed on your Linux device, then you need to add them with the following command: ` sudo apt-get install php php-bcmath php-curl php-zip`

### Download
Use the following command to download the tool: `git clone https://github.com/S3x0r/Lisk-Send-File/`
If you don’t have the permissions on the folder, you must remove the Lisk-Send-File folder first with `sudo rm -r Lisk-Send-File`. When you’ve removed the folder re-download the tool again with the following command: ` git clone https://github.com/S3x0r/Lisk-Send-File/`

### Configuration

Go to the folder Lisk-Send-File after you’ve downloaded it from GitHub. 
`cd Lisk-Send-File`

When you’re in the Lisk-Send-File folder you’ve to configure the `config.php` file
`sudo nano config.php`
```
For example:
/* Lisk address */
$GLOBALS['ADDRESS'] = '8273455169423958419L';
/* address passphrase */
$GLOBALS['PASSWORD'] = 'robust swift grocery peasant forget share enable convince deputy road keep cheap';
```
### Run
To run the tool, you must use the `sudo php send.php` command. When the tool is running it asks for a file. When you’ve entered the file, you need to enter yes or no. If you chose yes, you’ll get the Data ID to download the file, when no is entered the upload will be aborted.
