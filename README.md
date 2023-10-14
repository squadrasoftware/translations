# Sync translations

## About

As developers, it is more efficient to edit translations we need directly in our translation files than adding
translation keys on a SaaS web interface.

The idea of this project is to edit translations in our local files when adding a new feature, and once the feature is
ready to be delivered, we `sync` translations.

Syncing will compare local and remote files, ask interactively which changes should be kept to update local files, and
push the synced files to the provider.

## Installation

I didn't want to dockerize this project because it needs to access other projects' files.

Thus, you need to install php-cli in your environment:

- Install php 8.2 using your favorite package manager: `apt install php8.2-cli`


- Install composer: https://getcomposer.org/download/

## Usage

1. Clone this project in the squadra root directory


2. Copy `config.yaml.dist` into `config.yaml`.


3. Check that relative paths are valid.


4. Put your API token (see: https://crowdin.com/settings#api-key).


5. Run: `php bin/console sync <project-name>`

## Add a new project

Make sure to install crowdin-cli: https://crowdin.github.io/crowdin-cli/

In the project you want to integrate,

### .gitignore

Add `crowdin.yml` into `.gitignore` (it will contain an API token).

### Initialize crowdin

Run the following command:

```shell
crowdin init
```

Fill up the following information (adapt the values according to your project):

```yaml
"project_id": "619882"
"api_token": "..."
"base_path": "."
"base_url": "https://api.crowdin.com"
"preserve_hierarchy": true
files: [
  {
    "source": "src/i18n/en.json",
    "translation": "src/i18n/%two_letters_code%.json",
    "type": "json"
  }
]
```

### Push initial files

Upload translation files:

```shell
$ crowdin upload sources                                                          13/10/23 22:04:57
✔️  Fetching project info     
✔️  Directory 'src'
✔️  Directory 'src/i18n'
✔️  File 'src/i18n/en.json'
$ crowdin upload translations                                             10.3s  13/10/23 22:05:09
✔️  Fetching project info     
✔️  Translation file 'src/i18n/fr.json' has been uploaded
```

### Translate remaining strings

Crowdin does not integrate duplicates.

For example, the string:

- `en`: Transformation
- `fr`: Transformation

Won't be integrated in `fr` because it "thinks" that the string is not translated.

Just validate them manually on Crowdin interface and you'll be good to go.

Now, you can set-up a new project in `config.yml`.
