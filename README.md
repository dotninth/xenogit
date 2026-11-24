[![Stand With Ukraine](https://raw.githubusercontent.com/vshymanskyy/StandWithUkraine/main/banner-direct-single.svg)](https://supportukrainenow.org/)

<h1 align="center">
    <picture>
        <source media="(prefers-color-scheme: dark)" srcset=".github/assets/xenogit-logo-dark.svg">
        <img src=".github/assets/xenogit-logo-light.svg" alt="Xenogit">
    </picture>
</h1>

Xenogit sends your staged Git diff to Google Gemini to draft a one-line commit message. You can edit the message, ask for another draft, or cancel without committing.

<br>

## Install Xenogit

You need Git and PHP 8.2 or later with the Phar and zlib extensions.

To install with Composer, run:

```sh
composer global require dotninth/xenogit
```

If your shell can't find `xenogit`, run `composer global config bin-dir --absolute` and add the printed directory to `PATH`.

If you don't use Composer, download `xenogit` from the [latest GitHub release](https://github.com/dotninth/xenogit/releases/latest). Run `chmod +x xenogit` and move the file to a directory on `PATH`.

Check the installation with `xenogit --version`.

<br>

## Set your Gemini API key

Get a [Gemini API key from Google AI Studio](https://aistudio.google.com/apikey) and set the `API_KEY` environment variable.

To use a file instead, create `.env` next to the Xenogit executable. For a Composer install, run `composer global config vendor-dir --absolute` to find the vendor directory. Create `.env` in `dotninth/xenogit/builds` under that directory, not in Composer's bin directory.

Add this line and replace `YOUR_API_KEY` with your key:

```dotenv
API_KEY=YOUR_API_KEY
```

To have Xenogit write the file, see `xenogit help config:api-key`. The command appends a line. To replace a saved key, edit the existing `API_KEY` line in `.env`. Keep `.env` out of version control.

<br>

## Commit your staged changes

Review [Gemini API pricing](https://ai.google.dev/gemini-api/docs/pricing) and [Google's data use terms](https://ai.google.dev/gemini-api/terms) before sending code to Gemini. Each draft, including a regenerated draft, sends the staged diff in a new API request.

1. In your Git repository, stage the files you want to commit with `git add -- path/to/file`.
2. Run `git diff --staged` and check that the diff contains only code and data you can share with Google.
3. Ask Xenogit to draft the commit message:

   ```sh
   xenogit commit
   ```

4. At **Do you want to modify it?**, choose **Yes** to edit the draft, **No** to keep it, or **Regenerate** to request another draft.
5. At **Do you accept this commit message?**, choose **Yes** to run `git commit` or **No** to cancel.

Run `xenogit` to list the commands. Run `xenogit help commit` to see the options for `commit`.

<br>

## Use a different model

Xenogit uses `gemini-3.1-flash-lite` by default. To use a different model for one commit, pass its ID to `--model`:

```sh
xenogit commit --model=gemini-2.5-flash
```

The table lists the model IDs Xenogit supports and the defaults it uses. See [Google's Gemini model catalog](https://aistudio.google.com/docs/models) for details about the models.

| Model ID                 | `--thinking` levels                | Default `--tokens` |
| ------------------------ | ---------------------------------- | -----------------: |
| `gemini-2.5-flash`       | Not supported                      |                100 |
| `gemini-2.5-flash-lite`  | Not supported                      |                100 |
| `gemini-2.5-pro`         | Not supported                      |             65,536 |
| `gemini-3-flash-preview` | `MINIMAL`, `LOW`, `MEDIUM`, `HIGH` |             65,536 |
| `gemini-3.1-pro-preview` | `LOW`, `MEDIUM`, `HIGH`            |             65,536 |
| `gemini-3.1-flash-lite`  | `MINIMAL`, `LOW`, `MEDIUM`, `HIGH` |             65,536 |
| `gemini-3.5-flash`       | `MINIMAL`, `LOW`, `MEDIUM`, `HIGH` |             65,536 |
| `gemini-3.5-flash-lite`  | `MINIMAL`, `LOW`, `MEDIUM`, `HIGH` |             65,536 |
| `gemini-3.6-flash`       | `MINIMAL`, `LOW`, `MEDIUM`, `HIGH` |             65,536 |
| `gemini-3.7-flash`       | `LOW`, `MEDIUM`, `HIGH`            |             65,536 |
| `gemini-3.8-flash`       | `LOW`, `MEDIUM`, `HIGH`            |             65,536 |
| `gemini-4-carbon`        | `LOW`, `MEDIUM`, `HIGH`            |             32,768 |
| `gemini-4-argon`         | `LOW`, `MEDIUM`, `HIGH`            |             32,768 |
| `gemini-4-neon`          | `LOW`, `MEDIUM`, `HIGH`, `XHIGH`   |             65,536 |
| `gemini-4-hydrogen`      | `LOW`, `MEDIUM`, `HIGH`, `XHIGH`   |             65,536 |

Xenogit uses the first listed thinking level by default. Thinking level names are case-insensitive. Pass `--tokens` to override the default output token limit.

Gemini 4 models do not support `--temperature`. Xenogit uses `0.3` by default for other models.

<br>

## Contribute a change

1. Fork and clone the repository, then create a branch for your change.
2. Run `composer install` in the clone.
3. Follow the [project conventions](CONVENTIONS.md) and run the unit tests with `vendor/bin/pest --testsuite=Unit`.
4. Push your branch to your fork and open a pull request against `main`. Describe what changed and how you tested it.

<br>

## License

Xenogit uses the [MIT License](LICENSE.md).
