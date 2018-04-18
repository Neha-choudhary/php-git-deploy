# php-git-deploy
Use cronjobs to deploy from your git branch

## Status
**Work in progress**  


## Installation

From the Command Line:

```
git clone https://github.com/kaiquegazola/php-git-deploy.git
```

## Basic Usage

Get your api token from [GitLab](https://gitlab.com/profile/personal_access_tokens) or [GitHub](https://github.com/settings/tokens).

### For GitHub:

``` php
<?php

require_once './model/GitHub.class.php';

$git = new GitHub('PROJECT NAMESPACE', 'PROJECT PATH', 'PERSONAL ACCESS TOKEN');

$git->init();
```

### For GitLab:

``` php
<?php

require_once './model/GitLab.class.php';

$git = new GitLab('PROJECT NAMESPACE', 'PROJECT PATH', 'PERSONAL ACCESS TOKEN');

$git->init();
```

## Advanced Usage
### Constructors parameters
| Option | REQURIED | Default | Description |
| ------ | -------- | ------- | ----------- |
| PROJECT NAMESPACE | `yes` | none | The project namespace (aka username). |
| PROJECT PATH |`yes`| none | The project path (aka project name). |
| PERSONAL ACCESS TOKEN |`yes`| none | The personal access token for access to the GitHub/GitLab API. |
| CATCH COMMIT |`no`| `true`   | Catch commit's or release's: `TRUE = commit : FALSE = release` **(not ready yet)**. |
| BRANCH  |`no`| 'master' | The branch who will be pulled. |
| CONFIG FILE |`no`| 'git.json' | The config file name. |