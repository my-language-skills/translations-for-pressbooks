# plugin structure
```
plugins/translations-for-pressbooks/              # → Plugin root
├── doc/                                        # → Doc folder
├── vendor/                                     # → Vendor folder
├── flag-icon/                                  # → Images folder
│   │── all-flag.png                            # → all country flags
├── original-mark/                              # → Original mark folder
│   │── assets/                                 # → Assets folder
│       │── scripts/                            # → Script folder
│           └── original-mark-admin.js          # → Original mark script
│       └── original-mark.php                   # → Original mark php
├── wp-assets/                                  # → Images folder
│   │── all-banner.png                          # → Image
└── translations-for-pressbooks.php             # → Customise functions php of the plugging
