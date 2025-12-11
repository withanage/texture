(function() {
  "use strict";
  const TEXTURE_DAR_MANUSCRIPT_FILE = "manuscript.xml";
  pkp.registry.storeExtend(
    "fileManager_PRODUCTION_READY_FILES",
    (piniaContext) => {
      const dashboardStore = pkp.registry.getPiniaStore("dashboard");
      const fileStore = piniaContext.store;
      const { useModal } = pkp.modules.useModal;
      const { useLocalize } = pkp.modules.useLocalize;
      const { useUrl } = pkp.modules.useUrl;
      const { useFetch } = pkp.modules.useFetch;
      const { useDataChanged } = pkp.modules.useDataChanged;
      const { t, localize } = useLocalize();
      const { triggerDataChange } = useDataChanged();
      const { openDialog } = useModal();
      if (dashboardStore.dashboardPage !== "editorialDashboard" || fileStore.props.submissionStageId !== pkp.const.WORKFLOW_STAGE_ID_PRODUCTION) {
        return;
      }
      fileStore.extender.extendFn("getItemActions", (originalResult, args) => {
        let newResult = originalResult;
        const localizedName = localize(args.file.name);
        const { apiUrl } = useUrl(`submissions/texturePlugin/${args.file.id}`);
        if (localizedName.endsWith(".xml") && localizedName === TEXTURE_DAR_MANUSCRIPT_FILE) {
          console.log("args", args.file);
          newResult.push({
            label: t("plugins.generic.texture.links.editWithTexture"),
            name: "editor",
            icon: "FileText",
            actionFn: async ({ file }) => {
              const { fetch } = useFetch(`${apiUrl.value}/editor`, {
                method: "POST",
                headers: {
                  "Content-Type": "application/json",
                  "X-Csrf-Token": pkp.currentUser.csrfToken
                },
                body: file
              });
              await fetch().then(() => {
                console.log("fetch done");
              });
              console.log(t("plugins.generic.texture.links.editWithTexture") + " > clicked");
            }
          });
        }
        return [...newResult];
      });
    }
  );
})();
