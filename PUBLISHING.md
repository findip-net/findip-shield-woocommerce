# Publishing checklist

All official publication is performed on behalf of FindIP.

## GitHub

- Repository owner: `findip-net`
- Repository name: `findip-shield-woocommerce`
- Publishing account: `findip-bot`
- Commit identity: `FindIP <info@findip.net>`
- Never push, create releases, or change repository settings using a personal GitHub account.

Verify before pushing:

```bash
gh api user --jq .login
git config user.name
git config user.email
git remote -v
```

## Woo Marketplace

- Marketplace/vendor identity: FindIP
- Submission and support email: `info@findip.net`
- Security contact: `security@findip.net`
- Product type: SaaS integration or extension, according to the approved vendor agreement

Before submission:

1. Obtain approval for the FindIP Woo Marketplace vendor account.
2. Run CI and WooCommerce Quality Insights Toolkit checks.
3. Test a clean installation against supported WordPress and WooCommerce versions.
4. Test classic templates and Cart/Checkout Blocks with HPOS enabled.
5. Inspect all browser requests in every privacy and consent mode.
6. Confirm public privacy, terms, documentation, and support pages.
7. Prepare a reviewer account, test site, setup instructions, screenshots, and a short demo video.
8. Build the ZIP from `findip-shield-woocommerce/` only.

