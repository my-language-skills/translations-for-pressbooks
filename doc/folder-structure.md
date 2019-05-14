# plugin structure
```
plugins/translations-for-pressbooks/            # → Plugin root
├── assets/                                     # → Original mark folder
│   └── flag-icon/                              # → Assets folder with all flagicon
├── doc/                                        # → Doc folder
├── original-mark/                              # → Original mark folder
│   └── assets/                                 # → Assets folder
│       └── original-mark.php                   # → Original mark php
│       └── scripts/                            # → Script folder
│           └── original-mark-admin.js          # → Original mark script
├── vendor/                                     # → Vendor folder
│   └── plugin-update-checker/                  # → Update plugin with Admin panel (gitub)
├── wp-assets/                                  # → Images folder
│   └── all-banner.png                          # → Image
└── translations-for-pressbooks.php             # → Customise functions php of the plugging
