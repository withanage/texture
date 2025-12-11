/**
 * @file plugins/generic/docxConverter/resources/js/main.js
 *
 * Copyright (c) 2021-2025 TIB Hannover
 * Copyright (c) 2003-2025 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_docxconverter
 *
 * @brief Vite main file
 */

const TEXTURE_DAR_MANIFEST_FILE = 'manifest.xml';
const TEXTURE_DAR_MANUSCRIPT_FILE = 'manuscript.xml';
const TEXTURE_DAR_FILE_TYPE = 'dar';
const TEXTURE_ZIP_FILE_TYPE = 'zip';
const TEXTURE_HTML_FILE_TYPE = 'html';

pkp.registry.storeExtend('fileManager_PRODUCTION_READY_FILES', (piniaContext) => {
		const dashboardStore = pkp.registry.getPiniaStore('dashboard');
		const fileStore = piniaContext.store;
		const {useModal} = pkp.modules.useModal;
		const {useLocalize} = pkp.modules.useLocalize;
		const {useUrl} = pkp.modules.useUrl;
		const {useFetch} = pkp.modules.useFetch;
		const {useDataChanged} = pkp.modules.useDataChanged;

		const {t, localize} = useLocalize();
		const {triggerDataChange} = useDataChanged();
		const {openDialog} = useModal();

		function dataUpdateCallback() {
			triggerDataChange();
		}

		if (dashboardStore.dashboardPage !== 'editorialDashboard' || fileStore.props.submissionStageId !== pkp.const.WORKFLOW_STAGE_ID_PRODUCTION) {
			return;
		}

		fileStore.extender.extendFn('getItemActions', (originalResult, args) => {
			let newResult = originalResult;
			const localizedName = localize(args.file.name);
			const {apiUrl} = useUrl(`submissions/texturePlugin/${args.file.id}`);

			if (localizedName.endsWith('.xml') && localizedName === TEXTURE_DAR_MANUSCRIPT_FILE) {
				console.log('args', args.file);
				newResult.push({
					label: t('plugins.generic.texture.links.editWithTexture'),
					name: 'editor',
					icon: 'FileText',
					actionFn: async ({file}) => {
						const {fetch} = useFetch(`${apiUrl.value}/editor`, {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'X-Csrf-Token': pkp.currentUser.csrfToken,
							},
							body: file
						});
						await fetch().then(() => {
							console.log('fetch done');
						});
						console.log(t('plugins.generic.texture.links.editWithTexture') + ' > clicked');
					},
				});
			}

			return [...newResult];
		});
	}
);
