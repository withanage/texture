{**
 * plugins/generic/texture/templates/TextureArticleGalley.tpl
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Texture editor page
 *}

<script type="text/javascript">

    $(function () {ldelim}
        $('#CreateServiceFileForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
        {rdelim});

</script>

<form class="pkp_form" id="CreateServiceFileForm" method="post"
      action="{url op="createServiceFileForm" submissionId=$submissionId stageId=$stageId fileStage=$fileStage submissionFileId=$submissionFileId}">

    {csrf}

    {fbvFormArea id="serviceFileForm"}
    {fbvFormSection title="plugins.generic.texture.createServiceFile.file.name" required=true}
    {fbvElement type="text" label="plugins.generic.texture.createServiceFile.file.description" value=$serviceFile id="serviceFile" size=$fbvStyles.size.MEDIUM inline=true required=true}
    {/fbvFormSection}

    {fbvFormArea id="type"}
    {fbvFormSection title="common.type" required=true}
    {fbvElement type="select" from=$listOfServices id="serviceType" defaultLabel="common.chooseOne"|translate required=true}
    {/fbvFormSection}
    {/fbvFormArea}

    {fbvFormSection}
    {fbvElement type="select" id="serviceFileLocale" label="common.language" from=$supportedLocales selected=formLocale size=$fbvStyles.size.MEDIUM translate=false inline=true required=true}
    {/fbvFormSection}

    {/fbvFormArea}

    {fbvElement type="submit" class="submitFormButton" id="serviceFileSubmit" label="common.save"}

</form>
