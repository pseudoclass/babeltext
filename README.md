# Babeltext

## Multilingual Text Fieldtype for ExpressionEngine

By: [Pseudoclass](http://pseudoclass.com/)

There are plenty of ways to configure a full multilingual site in EE using modules like Transcribe, Biber, and Structure however, there are times when you may only need small portions of a site in multiple languages and it does not warrant a full multilingual framework.

![Example Babeltext Text Input](https://dl.dropboxusercontent.com/u/1671252/babeltext/sample_textinput_1.png)

Babeltext is a multilingual text entry fieldtype which will allow you to configure a multiple language text input for a single piece of text in an entry.

![Example Babeltext Textarea Input](https://dl.dropboxusercontent.com/u/1671252/babeltext/sample_rte.png)

![Example Babeltext Text Input](https://dl.dropboxusercontent.com/u/1671252/babeltext/sample_config.png)

### Features

1. Configure multiple languages for one piece of text. Set the order in which you would like the language tabs to appear on the entry form by dragging them up and down in the list.
2. Use regular text inputs, text areas, or the native ExpressionEngine Rich Text Editor.
3. Mark specific languages as required. If the custom field is set as required validation will fail unless the required language fields have text.
4. Set language direction for each language.
5. The template tag can take an optional "language" parameter to output the language specific version of the text. If no language is indicated or it is set to "dynamic" Babeltext will look in the URL string for a language code (i.e.; http://example.com/es/ for spanish), and output the language specific version that way.

## Usage

### Requirements

1. ExpressionEngine 2.5.3 and above
2. Native Rich Text Editor installed

### Installation

1. Upload the system/expressionengine/third_party/babeltext/ folder to your system/expressionengine/third_party/babeltext folder.
2. Upload the themes/third_party/babeltext/ folder to your themes/third_party/ folder
3. Go to Addons > Fieldtypes in your Control Panel and click Install to install the Babeltext fieldtype

### Configure Fieldtype

1. Create a new custom field for your channel and select its type as "Babeltext"
2. In the field options select the type of field you wish to use (Text Input, Textarea or Rich Text Editor)
3. In the languages table select a language you wish to add from the dropdown and then press "Add" to add it to the table.
4. To change the order in which the language tabs appear in the entry form, click and drag the languages in the table up and down.
5. If the custom field is set to required, you must check at least one of the languages in the table as required via the checkboxes.

### Tags

Use the custom fieldtype short name to output your content as normal. Here's a sample:

    {exp:channel:entries channel="channel_name"}

        <!-- Call the field directly --->
        <h3>{custom_field_name language="es"}</h3>

    {/exp:channel entries}

#### Parameters

The following parameters can be used in conjunction with the custom field tag:

**language** : Set this to the two letter ISO language code of the  language you wish to output. You could use a global variable in this case to set it dynamically (i.e.; {custom_field_name language="{lang_code}"}). If no language is set or the parameter is set to the default "dynamic", the Babeltext field will attempt to find the language code within the current URL structure (i.e.; http://example.com/de/ - displays the German version of the text). If the language was not set in the Babeltext field or none can be found, the tag will output the contents of the first language you configured in the list as a default.

## Roadmap

My initial plan is to release this fieldtype as a Beta to the EE Community to see if other EE Devs find it useful. If there is enough interest generated, I plan on working out the kinks, offering it on Devot:ee, and adding the following improvements:

* Caching and performance tweaks
* General code cleanup
* Spit-shine the interfaces a bit more
* Channel data updates on removing languages
* Compatibility with SafeCracker
* Compatibility with Low Variables
* Compatibility with Matrix and Wygwam
* Compatibility with Better Workflow
* Any suggestions you may have are welcome... Hit me up on [Twitter](https://twitter.com/pseudoclass) 

### Version History

* 0.1 Initial Beta Release (02/06/2013)