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
    <img src="https://img.shields.io/badge/release-v1.0.0-blue" alt="Project Version">
    <img src="https://img.shields.io/badge/php-%3E=8.1-royalblue" alt="PHP Version">
    <img src="https://img.shields.io/badge/license-MIT-green" alt="MIT License">
</h4>

## Introduction
`Xenogit` is your helpful command-line assistant that uses [OpenAI](https://openai.com/) GPT to quickly create commit messages that follow the [Conventional Commit](https://www.conventionalcommits.org/en/v1.0.0/) specification. Stop spending time writing commits and start managing your Git repository effortlessly!

## ️Getting Started

### Prerequisites
To run the project, you need to install [PHP](https://www.php.net/manual/en/install.php) and dependency manager [Composer](https://getcomposer.org) first.

### Installing
There are two ways to install Xenogit:

1. **Using Composer:** Run the following command to install Xenogit globally:

```shell
composer global require dotninth/xenogit
```

2. **Downloading from GitHub:** Visit the [Releases page](https://github.com/dotninth/xenogit/releases) on GitHub and download the appropriate binary.

## Usage
After installing Xenogit, you can create commit messages by using a command:

```shell
xenogit commit
```

## API Key
Xenogit needs an [API key from OpenAI](https://platform.openai.com/account/api-keys) to work properly. There are two options to provide the API key:

1. Using environment variable: Create an environment variable called `API_KEY` that contains your [OpenAI API key](https://platform.openai.com/account/api-keys).

2. Create a file named `.env` in the directory where the Xenogit binary is located. This file will be used to store your environment variables. To the `.env` file, add the following line, replacing `<YOUR_API_KEY>` with your actual [OpenAI API key](https://platform.openai.com/account/api-keys):

```shell
API_KEY=<YOUR_API_KEY>
```

## Payment

Using Xenogit will cost you money for each request you make to the OpenAI API. Xenogit uses the official ChatGPT (`gpt-3.5-turbo-16k`) model, which costs approximately 15 times less than GPT-4. Make sure you have enough funds or credits in your OpenAI account to pay for your usage of Xenogit.

To find out more about the price for using OpenAI's services, please check their pricing page at [OpenAI Pricing page](https://openai.com/pricing).

## Contribute
You are welcome to contribute to Xenogit! For a smooth collaboration, please follow these guidelines when contributing to the project:

1. Fork the repository and clone it to your local machine.
2. Create a new branch for your contribution: `git checkout -b my-contribution`.
3. Make your changes, making sure the code follows the project's coding style and conventions.
4. Test your changes thoroughly.
5. Commit your changes with a descriptive commit message, following the [Conventional Commit](https://www.conventionalcommits.org/) specification.
6. Push your branch to your forked repository: `git push origin my-contribution'.
7. Open a pull request against the `main' branch of the Xenogit repository.
8. Provide a clear and detailed description of your changes in the pull request, along with any relevant information or context.

Please note that by contributing to Xenogit, you agree to release your contributions under the [MIT License](LICENSE.md).

## License

Xenogit is released under the [MIT License](LICENSE.md). Feel free to modify and distribute it according to your needs.
