# TODO
- Fix loading indicators
- Support custom sizes that can be passed by user
- Support more providers like pexels, bing images etc
- Add support for multipe uploads
- Add Full pagination
- Add support for Spatie Media Plugin

  - CleanupUnusedUploadedFile should add support Spatie Media
  - `$component->saveUploadedFiles()` saves the file to DB directly, which wouldnt work on create record page
  - if dont run `$component->saveUploadedFiles()`, there is no way we can show a preview of uploaded file from url

  We have two exceptions for adding support for spatie media

  1. It can only be used on Edit record page and it would forcefully auto save the picked file.
  2. User has to blindly save the form without viewing the preview.

  Workaround:

- Write tests
