Texture plugin for OJS3
=======================
### About
This plugin integrates the Texture editor with OJS workflow for direct editing of JATS XML documents.
### Supported  Body Tags
Tag| Support| Example| | 
| --- | --- | --- | --- 
[`code`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/code.html)| :heavy_check_mark:| `<code     code-type="user interface control"   language="C++"  language-version="11"  xml:space="preserve"   orientation="portrait"  position="anchor">#include &lt;conio.h>#include&lt;win_mous.cpp>// Needed for mouse &amp; win functions#defineOK (x>=170 &amp;&amp; x&lt;=210 &amp;&amp; y>=290 &amp;&amp; y&lt;=310)#defineCANCEL (x>=280 &amp;&amp; x&lt;=330 &amp;&amp; y>=290 &amp;&amp; y&lt;=310)#define PUSHME (x>=170 &amp;&amp; x&lt;=330 &amp;&amp; y>=150 &amp;&amp; y&lt;=250)</code>`
[`disp-formula`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/disp-formula.html)| :heavy_check_mark:| `<disp-formula><tex-math id="M1"><![CDATA[\documentclass[12pt]{minimal}\usepackage{wasysym}\usepackage[substack]{amsmath}\usepackage{amsfonts}\usepackage{amssymb}\usepackage{amsbsy}\usepackage[mathscr]{eucal}\usepackage{mathrsfs}\DeclareFontFamily{T1}{linotext}{}\DeclareFontShape{T1}{linotext}{m}{n} { &#x003C;-&#x003E; linotext }{}\DeclareSymbolFont{linotext}{T1}{linotext}{m}{n}\DeclareSymbolFontAlphabet{\mathLINOTEXT}{linotext}\begin{document}$${\mathrm{Acc/Acc:\hspace{.5em}}}\frac{{\mathit{ade2-202}}}{{\mathit{ADE2}}}\hspace{.5em}\frac{{\mathit{ura3-59}}}{{\mathit{ura3-59}}}\hspace{.5em}\frac{{\mathit{ADE1}}}{{\mathit{adel-201}}}\hspace{.5em}\frac{{\mathit{ter1-Acc}}}{{\mathit{ter1-Acc}}}\hspace{.5em}\frac{{\mathit{MATa}}}{{\mathit{MAT{\alpha}}}}$$\end{document}]]></tex-math></disp-formula>`
[`disp-quote`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/disp-quote.html)| :heavy_check_mark:| `<disp-quote><p>Dead flies cause the ointment of the apothecary to send forth astinking savor; so doth a little folly him that is in reputationfor wisdom and honour.</p><attrib>Ecclesiastes 10:1</attrib></disp-quote>`
[`fig-group`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/fig-group.html)| :heavy_check_mark:| `<fig-group id="dogpix4">  <caption><title>Figures 12-14 Bonnie Lassie</title>  <p>Three perspectives on My Dog</p></caption>  <fig id="fg-12">   <label>a.</label>   <caption><p>View A: From the Front, Laughing</p></caption>   <graphic xlink:href="frontView.png"/>  </fig>  <fig id="fg-13">   <label>b.</label>   <caption><p>View B: From the Side, Best Profile</p></caption>   <graphic xlink:href="sideView.png"/>  </fig>  <fig id="fg-14">   <label>c.</label>   <caption><p>View C: In Motion, A Blur on Feet</p></caption>   <graphic xlink:href="motionView.png"/>  </fig></fig-group>`
[`fig`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/fig.html)| :heavy_check_mark:| `<fig id="f1" orientation="portrait" position="float"><graphic xlink:href="f1"/><attrib>Brookhaven National Laboratory</attrib></fig>`
[`graphic`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/graphic.html)| :heavy_check_mark:| `<graphic xlink:href="f1"/>`
[`list`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/list.html)| :heavy_check_mark:| `<list list-type="bullet"><list-item><p>The benefits of geriatric day hospital care have beencontroversial for many years.</p></list-item><list-item><p>This systematic review of 12 randomised trials comparinga variety of day hospitals with a range of alternativeservices found no overall advantage for day hospital care.</p></list-item><list-item><p>Day hospitals had a possible advantage over no comprehensivecare in terms of death or poor outcome, disability, and use ofresources.</p></list-item><list-item><p>The costs of day hospital care may be partly offset bya reduced use of hospital beds and institutional care amongsurvivors.</p></list-item></list>`
[`p`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/p.html)| :heavy_check_mark:| `<p>Geriatric day hospitals developed rapidly in the United Kingdom in the 1960sas an important component of care provision. The model has since been widelyapplied in several Western countries. Day hospitals provide multidisciplinaryassessment and rehabilitation in an outpatient setting and have a pivotalposition between hospital and home based services. ...</p>`
[`preformat`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/preformat.html)| :heavy_check_mark:| `<preformat preformat-type="dialog">C:\users\lap make  'make' is not recognized as:    - an internal or external command    - an operable program    - a batch file</preformat>`
[`sec`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/sec.html)| :heavy_check_mark:| `<sec sec-type="intro"><title>Introduction</title><p>Geriatric day hospitals developed rapidly in the United Kingdomin the 1960s as an important component of care provision. The modelhas since been widely applied in several Western countries. Dayhospitals provide multidisciplinary assessment and rehabilitationin an outpatient setting and have a pivotal position between hospitaland home based services. ... We therefore undertook a systematicreview of the randomized trials of day hospital care.</p></sec>`
[`supplementary-material`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/supplementary-material.html)| :heavy_check_mark:| `<supplementary-material mime-subtype="zip" mimetype="application"xlink:href="ASASTD.ANSI.ASA.S3.50.supplementary-material.zip"/>`
[`table-wrap`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/table-wrap.html)| :heavy_check_mark:| `<table-wrap id="t2" orientation="portrait" position="float"><label>Table II.</label><caption><p>Models to approximate the bound frequencies as waves in X→M (<inline-graphic id="g1" xlink:href="d1"/>: Rotational, <inline-graphic id="g2" xlink:href="d2"/>: Vibrate in <italic>y</italic> direction, <inline-graphic id="g3" xlink:href="d3"/>: Vibrate in<italic>x</italic> direction, <inline-graphic id="g4" xlink:href="d4"/>: Vibrate mainly in <italic>y</italic> direction including a small portion of vibration in <italic>x</italic> direction, <inline-graphic id="g5" xlink:href="d5"/>: Vibrate mainly in <italic>x</italic> direction including a small portion of vibration in <italic>y</italic> direction).</p></caption><table border="1">...</table></table-wrap>`
[`tex-math`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/tex-math.html)| :heavy_check_mark:| `<tex-math id="M1"><![CDATA[\documentclass[12pt]{minimal}\usepackage{wasysym}\usepackage[substack]{amsmath}\usepackage{amsfonts}\usepackage{amssymb}\usepackage{amsbsy}\usepackage[mathscr]{eucal}\usepackage{mathrsfs}\DeclareFontFamily{T1}{linotext}{}\DeclareFontShape{T1}{linotext}{m}{n} { &#x003C;-&#x003E; linotext }{}\DeclareSymbolFont{linotext}{T1}{linotext}{m}{n}\DeclareSymbolFontAlphabet{\mathLINOTEXT}{linotext}\begin{document}$${\mathrm{Acc/Acc:\hspace{.5em}}}\frac{{\mathit{ade2-202}}}{{\mathit{ADE2}}}\hspace{.5em}\frac{{\mathit{ura3-59}}}{{\mathit{ura3-59}}}\hspace{.5em}\frac{{\mathit{ADE1}}}{{\mathit{adel-201}}}\hspace{.5em}\frac{{\mathit{ter1-Acc}}}{{\mathit{ter1-Acc}}}\hspace{.5em}\frac{{\mathit{MATa}}}{{\mathit{MAT{\alpha}}}}$$\end{document}]]></tex-math>`
### Not yet supported  Body Tags
Tag| Support| Example| | 
| --- | --- | --- | --- 
[`ack`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/ack.html)| --
[`address`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/address.html)| --
[`alternatives`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/alternatives.html)| --
[`array`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/array.html)| --
[`boxed-text`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/boxed-text.html)| --
[`chem-struct-wrap`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/chem-struct-wrap.html)| --
[`def-list`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/def-list.html)| --
[`disp-formula-group`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/disp-formula-group.html)| --
[`media`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/media.html)| --
[`related-article`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/related-article.html)| --
[`related-object`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/related-object.html)| --
[`sig-block`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/sig-block.html)| --
[`speech`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/speech.html)| --
[`statement`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/statement.html)| --
[`table-wrap-group`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/table-wrap-group.html)| --
[`verse-group`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/verse-group.html)| --
[`x`](https://jats.nlm.nih.gov/archiving/tag-library/1.3d1/element/x.html)| --
### Installation
Texture is available under Plugin gallery
 
* Settings -> Web site -> Plugins -> Plugin gallery 
![texture_plugin](docs/plugin_gallery.png)
### Usage
Texture supports editing XML files in [JATS](https://jats.nlm.nih.gov/archiving/1.1/) XML standard.
* After plugin installation,  go to a `Production Stage` of the submission
* Upload JATS XML to the  `Production Ready` state. You can find sample files [blank manuscript](https://github.com/substance/texture/tree/master/data/blank) or a [list of samples](https://github.com/substance/texture/tree/master/data/) here.
![production_ready_edit](docs/production_ready_edit.png)
* All the uploaded images in texture are integrated as dependent files in production ready stage.
* When you later publish the texture-edited JATS XML file as galley, you have to upload the images **again** in the dependancy grid.
![gallery_edit](docs/galley_edit.png)
* In the editing modal, upload the same images as dependent files you uploaded for texture.  
### Issues
Please find any issues here 
* https://github.com/pkp/texture/issues
