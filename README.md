# SilverStripe Fluent Tweaks

Simple experimental to add some extra options to Fluent Locale:

- set order/sorting
- hide from LocaleMenu
- hide from MetaTags

Private project, no help/support provided

## Requirements

* silverstripe/cms ^4.2
* silverstripe/vendor-plugin: ^1.0
* tractorcow/silverstripe-fluent ^4 || ^5


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
  
  Derralf\FluentTweaks\LocaleExtension:
    HiddenTitlePrefix: '* '
    HiddenTitleSuffix: ' (!)'
  ```

## Configuration & Templating

- Go to Locale Admin and add value for sorting to each locale
- You may use the same value for multiple locales to create "groups" awithin which alphanumeric sorting takes place
- select the "Hidden" chackbox to hide the Locale from the frontend
- optionally go to security and define which user groups/editors can see hidden locales

**Caution:** Published Pages/Contents for "hidden" Locales will still be public/accessible anyway. They are just hidden from the Language Menu and/or Meta Tags (alternate links).

**Caution: NEVER EVER** name the function `FilteredLocales()` if you want to use `FluentFilteredExtension`on SiteTree :)

In your Page.php

```
        // custom FILTERING/sorting/order for Locales
        // original: silverstripe-fluent/src/Extension/FluentExtension.php
        public function PublicVisibleLocales()
        {
            $data = [];
            if (Locale::getCached()) {
                foreach (Locale::getCached() as $localeObj) {

                    $locale = $localeObj->getLocale();

                    /** @var Locale $localeObj */
                    $info = $this->owner->LocaleInformation($localeObj->getLocale());

                    // optional: add Hidden flag, maybe for templating/styling
                    $info->Hidden = $localeObj->Hidden;

                    // optional/test: add ID for sorting
                    $info->ID = $localeObj->ID;

                    // optional: add bootstrap linking mode 'active'
                    $info->LinkingMode = ($info->LinkingMode == 'current') ? ($info->LinkingMode . ' active') : $info->LinkingMode;

                    // Fluent 4 only (not needed with Fluent 5): is page published in Locale? -> Filter for alternate links in FluentSiteTree_MetaTags.ss
                    // $info->isPublished = ($this->owner->isPublishedInLocale($locale)) ? true : false;

                    // if Hidden-Field exists on object and Hidden is set: continue and don't add to array
                    // or check if member can view hidden locales
                    // and add prefix/suffix, if defined
                    if ($localeObj->Hidden) {
                        if (!$localeObj->canViewHidden()) {
                            continue;
                        }
                        if ($localeObj->Hidden && $prefix = Config::inst()->get('Derralf\FluentTweaks\LocaleExtension', 'HiddenTitlePrefix')) {
                            $info->Title = $prefix . $info->Title;
                            $info->Language = $prefix . $info->Language;
                        }
                        if ($localeObj->Hidden && $suffix = Config::inst()->get('Derralf\FluentTweaks\LocaleExtension', 'HiddenTitleSuffix')) {
                            $info->Title = $info->Title . $suffix;
                            $info->Language = $info->Language . $suffix;
                        }
                    }
                    // add new data to array
                    $data[] = $info;
                }
            }
            return ArrayList::create($data);
            // return ArrayList::create($data)->sort('ID ASC');
        }
  
```

### Templates

Override LocaleMenu.ss in templates/Includes/LocaleMenu.ss

```
# Fluent 4
<% if $PublicVisibleLocales %>
<div class="left">Locale <span class="arrow">&rarr;</span>
	<nav class="primary">
		<ul>
			<% loop $PublicVisibleLocales %>
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
# FLuent 5
<% if $LinkToXDefault %>
    <link rel="alternate" hreflang="x-default" href="{$absoluteBaseURL.ATT}" />
<% end_if %>
<% if $PublicVisibleLocales %><% loop $PublicVisibleLocales %><% if $LinkingMode != 'invalid'  && $isPublished && $canViewInLocale %>
    <link rel="alternate" hreflang="$LocaleRFC1766.ATT" href="$AbsoluteLink.ATT" />
<% end_if %><% end_loop %><% end_if %>

# Fluent 4
<% if $PublicVisibleLocales %><% loop $PublicVisibleLocales %><% if $LinkingMode != 'invalid' %>
	<link rel="alternate" hreflang="$LocaleRFC1766.ATT" href="$AbsoluteLink.ATT" />
<% end_if %><% end_loop %><% end_if %>
```
