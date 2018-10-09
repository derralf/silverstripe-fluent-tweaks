# SilverStripe Fluent Tweaks

Simple experimental to add some extra options to Fluent Locale:

- set order/sorting
- hide from LocaleMenu
- hide from MetaTags

Private project, no help/support provided

## Requirements

* silverstripe/cms ^4.2
* silverstripe/vendor-plugin: ^1.0
* tractorcow/silverstripe-fluent ^4


## Installation

- Install the module via Composer

  ```
  composer require derralf/silverstripe-fluent-tweaks
  ```

- In your config.yml (or fluentconfig.yml, etc):

  ```
  TractorCow\Fluent\Model\Locale:
    extensions:
    - Derralf\FluentTweaks\LocaleExtension
    default_sort: 'Sort ASC, "Fluent_Locale"."Locale" ASC'
    summary_fields:
    - Sort
    - Hidden
  ```

## Configuration & Templating

- Go to Locale Admin and add value for sorting to each locale
- You may use the same value for multiple locales to create "groups" awithin which alphanumeric sorting takes place
- select the "Hidden" chackbox to hide the Locale from the frontend

**Caution:** Published Pages/Contents for "hidden" Locales will still be public/accessible anyway. They are just hidden from the Language Menu and/or Meta Tags (alternate links).

In your Page.php

```
    // custom sorting/order for Locales
    // original: silverstripe-fluent/src/Extension/FluentExtension.php
    public function FilteredLocales()
    {
        $data = [];
        if (Locale::getCached()) {
            foreach (Locale::getCached() as $localeObj) {
                /** @var Locale $localeObj */
                $info = $this->owner->LocaleInformation($localeObj->getLocale());
                
                // optional/test: add ID for sorting
                // $info->ID = $localeObj->ID;
                
                // optional: add bootstrap linking mode 'active'
                $info->LinkingMode = ($info->LinkingMode == 'current') ? ($info->LinkingMode . ' active') : $info->LinkingMode;
                
                // if Hidden-Field exists on object and Hidden is set: continue and don't add to array
                if($localeObj->hasField('Hidden') && $info->Hidden){
                    continue;
                }
                
                // add new data to array
                $data[] = $info;
            }
        }
        return ArrayList::create($data);
        // return ArrayList::create($data)->sort('ID ASC');
    }
  
```

Override LocaleMenu.ss in templates/Includes/LocaleMenu.ss

```
<% if $FilteredLocales %>
<div class="left">Locale <span class="arrow">&rarr;</span>
	<nav class="primary">
		<ul>
			<% loop $FilteredLocales %>
				<li class="$LinkingMode">
					<a href="$Link.ATT" <% if $LinkingMode != 'invalid' %>rel="alternate" hreflang="$LocaleRFC1766"<% end_if %>>$Title.XML</a>
				</li>
			<% end_loop %>
		</ul>
	</nav>
</div>
<% end_if %>
```

Override FluentSiteTree_MetaTags.ss in templates/Includes/FluentSiteTree_MetaTags.ss

```
<% if $FilteredLocales %><% loop $FilteredLocales %><% if $LinkingMode != 'invalid' %>
	<link rel="alternate" hreflang="$LocaleRFC1766.ATT" href="$AbsoluteLink.ATT" />
<% end_if %><% end_loop %><% end_if %>
```
