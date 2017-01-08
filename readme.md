# Allow for dynamic blobbed settings in Eloquent models.

This package was created for an internal project, but as the idea is reusable, I encourage others to make use of this package.

The main goal was to create a way to store variables dynamicly into a database field without having to rewrite the database structure.

## Usage
Just use the HasSettings trait in your Eloquent model and add a field named ```settings``` to your table with a type of ```MEDIUMTEXT```.

You can also use another field name, but be sure to set the ```protected $settingsField``` to that specific fieldname.