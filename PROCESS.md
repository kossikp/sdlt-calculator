# PROCESS

## 1) How long this actually took

This took me roughly two hours end-to-end (GitHub repo setup, project setup using latest Laravel and PHP version, implementation, testing, and documentation updates).

The rough split was:
- Initial setup and environment checks: ~2 hours
- SDLT requirements shaping and implementation: ~5-6 hours
- Validation, test writing, and calculator refinement: ~2-3 hours
- README/cleanup and final pass: ~1 hour

## 2) How I used AI tooling

I used AI tools in three distinct ways:

- **ChatGPT**
  - I used ChatGPT to re-arrange the requirements into Functional and Non-functional requirements so implementation scope was explicit before coding.

- **PhpStorm AI chat**
  - I used PhpStorm AI chat to fix some Laravel server issues during setup/troubleshooting.

- **Cursor**
  - I used Cursor to complete the full project development (routes, controller, service, Blade UI, config-driven rates, tests, and docs).

### One output I rejected/rewrote (specific example)

I rewrote an early controller-validation approach that used a separate empty validator call for post-validation checks.  
I replaced it with a single `Validator::make(...)` flow plus an `after(...)` hook in the same validator instance, then called `validate()` once.

Why I changed it:
- Keeps all validation concerns in one place
- Cleaner Laravel style and easier to reason about
- Reduces the chance of validation drift/duplication over time

## 3) How I verified the maths

The maths are verified using post-April 2025 bands, FTB cap, and 5% surcharge, as mentioned primarily.

What I checked:
- Standard progressive residential bands
- First-time buyer relief eligibility and cap behavior
- Additional property surcharge stacked across relevant bands
- Boundary values at exact thresholds

How I checked:
- Cross-checked rate assumptions against HMRC/GOV.UK guidance for current SDLT rules
- Encoded those values in `config/sdlt.php`
- Added automated unit tests for scenario coverage and threshold edge cases
- Added feature tests to validate request/response behavior and result rendering
- Added README sanity-check examples and compared expected outputs with calculator results

What I found:
- The implemented outputs are consistent with the configured post-April 2025 rate model used in this project.
- First-time buyer relief correctly falls back to standard rates when the cap is exceeded.
- Additional property surcharge behavior is applied consistently across bands in the current implementation.

## 4) What I’d do with another hour

With another hour, I will think and consider the application design and architecture (CAP theorem, etc), refactor the application code using PSR-12, and design patterns for code maintainability and reusability.

In practical terms for this codebase, I would focus that hour on:
- Reviewing architecture decisions and documenting trade-offs briefly
- Refactoring service/controller boundaries for clearer extension points
- Tightening naming and small structural improvements for maintainability
- Expanding tests with a few additional edge-case and regression scenarios
