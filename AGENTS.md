# Project Guidelines

## Mandatory Planning Workflow

Before modifying, creating, or deleting any project file:

1. Analyze the user's requirements.
2. Inspect the relevant parts of the codebase.
3. Identify affected files and dependencies.
4. Identify potential risks and edge cases.
5. Propose a clear implementation plan in Persian.
6. Explain the purpose of each proposed change.
7. Wait for explicit user approval before making any changes.

Do not modify any files before receiving explicit approval.

## Architecture
- Follow SOLID principles.
- Prefer simple and maintainable solutions.
- Avoid unnecessary abstractions.
- Use DDD when justified by business complexity.

## Laravel
- Keep controllers thin.
- Use Form Requests for validation.
- Use dependency injection.
- Never use env() outside config files.
- Follow Laravel conventions.

## Development
- Do not modify unrelated files.
- Do not run migrations without approval.
- Explain architectural decisions before implementation.
