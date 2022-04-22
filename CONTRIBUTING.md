# To contribute you need to follow this work flow

- Create an issue
  - Title: A short description, starting with a cap and with no point at the end.
    - Example: `Add fixtures and readme`
  - Description: If needed for more information about the task.
  - Assignees: You need to assign the task to yourself.

- Create a new branch named using this pattern: `issue_number-short_issue_title`.
  - Example: Your issue is the 15th one and whit "Add fixtures and readme" as title. Your branch name will be `15-fixtures-readme`.

- Commit and push as many times you need.
  - Commit naming: 
    - Each commit must start with a verb conjugated to the past participle (`added`, `fixed`, `refactored`, ...). Next a short description of the content of the commit.

    - The commit name must be in lowercase.

- Open a pull request:
  - Name: Your pull request must have the same name as your branch without the dashed.
    - Example: Your branch is `15-fixtures-readme` so your pull request is `15 fixtures readme`.
  - Description: 
    - Must start with `closed #issue_id` so GitHub can auto link the issue with the pull request.

    - After that you can add if needed more information about the purpose of your pull request.

  - Reviewers: You must assign the project supervisor as reviewers.
  - Assignees: You need to assign the pull request to yourself.

- Merge the pull request:
  - Once your pull request has been reviewed by the project supervisor you should:
    - Merge the pull request.
    - Delete your branch.
