Fooman Email Attachments
===================

### Installation Instructions
To install the extension, follow the steps in [The Installation Guide](http://magento1-support.fooman.co.nz/article/354-installing-fooman-email-attachments).

### Installation Options

**via composer**  
Fooman extension are included in the packages.firegento.com repository so you can install them easily via adding the extension to the require section and then running `composer install` or `composer update`

    "require":{
      "fooman/emailattachments":"*"
    },

Please note that packages.firegento.com might not always up-to-date - in this case please add the following in the repositories section

    "repositories":[
      {
        "type":"composer",
        "url":"http://packages.fooman.co.nz"
      }
    ],

**via modman**  
`modman clone https://github.com/fooman/common.git`   
`modman clone https://github.com/fooman/emailattachments.git`   

**via file transfer (zip download)**  
    please see the releases tab for https://github.com/fooman/emailattachments/releases
    and https://github.com/fooman/common/releases
