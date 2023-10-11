# P6_OC-SnowTricks
Développez de A à Z le site communautaire SnowTricks

## Environnement de développement
* Symfony 6.3
* Composer 2.6
* Bootstrap 4.0.0
* jQuery 3.2.1
* PHPUnit 9.5
* WampServer 3.2.6
    * Apache 2.4.51
    * PHP 8.1.4
    * MySQL 5.7.36
 
## Respect des bonnes pratique
Utilisation de [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
Codacy badge

## Installation du projet
1.Télécharger ou cloner le repository suivant:
```
https://github.com/MaximeHoup/P6_OC-SnowTricks.git
```

2.Configurez vos variables d'environnement (connexion à la base de données, serveur SMTP...) à l'aide du fichier
```.env```

3.Téléchargez et installez les dépendances du projet avec [Composer](https://getcomposer.org/download/) :
```
    composer install
```

4.Créez la base de données grace à la commande:
```
    php bin/console doctrine:database:create
```

5.Créez les différentes tables de la base de données avec la commande :
```
    php bin/console doctrine:migrations:migrate
```

6.(Optionnel) Installer les fixtures pour avoir une démo avec des données fictives :
```
    php bin/console doctrine:fixtures:load
```
