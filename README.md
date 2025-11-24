<h1 align="center">
    <a href="https://github.com/dotninth/xenogit/#gh-light-mode-only">
        <img src="./.github/assets/xenogit-logo-light.svg" alt="Xenogit Repository">
    </a>
    <a href="https://github.com/dotninth/xenogit/#gh-dark-mode-only">
        <img src="./.github/assets/xenogit-logo-dark.svg" alt="Xenogit Repository">
    </a>
</h1>

<p align="center">
    <i align="center">Your CLI buddy for instant Git commit messages.</i>
</p>

<h4 align="center">
    <img src="https://img.shields.io/badge/v2.3.4-stable?style=for-the-badge&color=C9CBFF&labelColor=302D41&label=stable" alt="Latest Stable Version">
    <img src="https://img.shields.io/badge/v2.3.4-beta?style=for-the-badge&color=f5a97f&labelColor=302D41&label=beta" alt="Latest Beta Version">
    <img src="https://img.shields.io/badge/8.2-php_version?style=for-the-badge&color=89dceb&labelColor=302D41&label=php" alt="PHP Version">
    <img src="https://img.shields.io/badge/MIT-license?style=for-the-badge&color=cba6f7&labelColor=302D41&label=license" alt="MIT License">
</h4>

<br />

## Introduction

`Xenogit` is your helpful command-line assistant that uses [Google Gemini](https://deepmind.google/technologies/gemini/) to quickly create commit messages.
Stop spending time writing commits and start managing your Git repository effortlessly!

<br />

## ️Getting Started

### Installing

There are two ways to install Xenogit:

1. **Using Composer:** Run the following command to install Xenogit globally:

```shell
composer global require dotninth/xenogit
```

2. **Downloading from GitHub:** Visit the [Releases page](https://github.com/dotninth/xenogit/releases) on GitHub and download the appropriate binary.

<br />

## Usage

After installing Xenogit, you can all available options by simply launcing the program:

```shell
$ xenogit

  Xenogit  v2.3.3

  USAGE:  <command> [options] [arguments]

  commit         Automatically generate commit messages

  config:api-key Configure the API key in the .env file
```

```shell
$ xenogit help commit

Description:
  Automatically generate commit messages

Usage:
  commit [options]

Options:
  -m, --model[=MODEL]              Set the ID of the model to use (optional). Default: gemini-2.5-flash-lite
  -t, --temperature[=TEMPERATURE]  Set the temperature (optional). Default: 0
  -k, --tokens[=TOKENS]            Set the maximum number of tokens to use (optional). Default: 100
  -h, --help                       Display help for the given command. When no command is given display help for the list command
  -q, --quiet                      Do not output any message
  -V, --version                    Display this application version
      --ansi|--no-ansi             Force (or disable --no-ansi) ANSI output
  -n, --no-interaction             Do not ask any interactive question
      --env[=ENV]                  The environment the command should run under
  -v|vv|vvv, --verbose             Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

<br />

## Currently available models

You can use 6 models from Google that you can use with the `-m` flag:

#### Gemini 2.0

- Gemini 2.0 Flash - `gemini-2.0-flash`
- Gemini 2.0 Flash Lite - `gemini-2.0-flash-lite`

#### Gemini 2.5

- Gemini 2.5 Flash - `gemini-2.5-flash`
- Gemini 2.5 Flash Preview - `gemini-2.5-flash-preview-09-2025`
- Gemini 2.5 Flash Lite - `gemini-2.5-flash-lite`
- Gemini 2.5 Flash Lite Preview - `gemini-2.5-flash-lite-preview-09-2025`
- Gemini 2.5 Pro - `gemini-2.5-pro`

#### Gemini 3.0

- Gemini 3.0 Pro - `gemini-3-pro-preview`

> [!NOTE]
> Note that **Gemini 2.5** models takes longer and costs more to generate a commit. But if you have a huge set of changes (huge commit), it works best.

<br />

## API Key

Xenogit needs an [API key from Google AI Studio](https://aistudio.google.com/apikey) to work properly. There are two options to provide the API key:

1. Use the command `xenogit config:api-key <your-api-key>`.

2. Using environment variable: Create an environment variable called `API_KEY` that contains your [Gemini API key](https://aistudio.google.com/apikey).

3. Create a file named `.env` in the directory where the Xenogit binary is located. This file will be used to store your environment variables. To the `.env` file, add the following line, replacing `<YOUR_API_KEY>` with your actual [Gemini API key](https://aistudio.google.com/apikey):

```shell
API_KEY=<YOUR_API_KEY>
```

<br />

## Payment

Using Xenogit will cost you money for every request you make to the Gemini API. Xenogit uses the official Gemini 2.5 Flash Lite (`gemini-2.5-flash-lite`) model,
which is the best model in terms of price/quality ratio. At least for the task Xenogit solves.

You can also use Free Tier for Gemini. Be sure to check for limitations and be aware that Google will use your data to improve their products.

To find out more about the price for using Google's services, please check their pricing page at [Gemini Pricing page](https://ai.google.dev/gemini-api/docs/pricing).

<br />

## Bash aliases example

To simplify common workflows, such as adding files to git and committing them, I recommend creating bash aliases.

Here are mine _(sort of a mnemonic for aliases)_:

```zsh
alias xc="xenogit commit"
alias axc="git add . && xc"
alias xcp="xc && git push"
alias axcp="axc && git push"
```

<br />

## Contribute

You are welcome to contribute to Xenogit! For a smooth collaboration, please follow these guidelines when contributing to the project:

1. Fork the repository and clone it to your local machine.
2. Create a new branch for your contribution: `git checkout -b my-contribution`.
3. Make your changes, making sure the code follows the project's coding style and conventions.
4. Test your changes thoroughly.
5. Commit your changes with a descriptive commit message.
6. Push your branch to your forked repository: `git push origin my-contribution'.
7. Open a pull request against the `main' branch of the Xenogit repository.
8. Provide a clear and detailed description of your changes in the pull request, along with any relevant information or context.

Please note that by contributing to Xenogit, you agree to release your contributions under the [MIT License](LICENSE.md).

<br />

## License

Xenogit is released under the [MIT License](LICENSE.md). Feel free to modify and distribute it according to your needs.
