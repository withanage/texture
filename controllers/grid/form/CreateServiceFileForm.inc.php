<?php
import('lib.pkp.classes.form.Form');

class CreateServiceFileForm extends Form
{


    private Publication $publication;
    private Request $request;
    protected string $serviceFile;
    protected string $serviceType;


    /**
     * @return string
     */

    private string $serviceFileLocale;
    private Submission $submission;

    private array $listOfServices = ['plugins.generic.texture.createServiceFile.orkg'];

    /**
     * @return array
     */

    /**
     * Constructor for the service file creation form
     */
    public function __construct(Request $request, Plugin $plugin, Publication $publication, Submission $submission)
    {
        parent::__construct($plugin->getTemplateResource('ServiceFile.tpl'));

        $this->publication = $publication;
        $this->request = $request;
        $this->submission = $submission;

        AppLocale::requireComponents(LOCALE_COMPONENT_APP_EDITOR, LOCALE_COMPONENT_PKP_SUBMISSION);
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }


    public function execute(...$functionArgs): void
    {


        switch ($this->getServiceType()) {
            case 0:  // orkg
                import('plugins.generic.texture.handlers.ORKGFileHandler');
                $handler = new ORKGFileHandler();
                break;
            default:
                $this->handle404('Service Implementation for ' . $this->listOfServices[$this->getServiceType()] . ' not found');
        }
        ;

        $this->downloadServiceFile($handler);
    }

    /**
     * Fetches and displays the form template
     */
    public function fetch($request, $template = null, $display = false): string
    {
        $context = $request->getJournal();
        $templateMgr = TemplateManager::getManager($request);

        $templateMgr->assign([
            'fileStage' => $request->getUserVar('fileStage'),
            'stageId' => $request->getUserVar('stageId'),
            'submissionId' => $this->submission->getId(),
            'supportedLocales' => $context->getSupportedSubmissionLocaleNames(),
            'listOfServices' => $this->getListOfServices(),
        ]);

        return parent::fetch($request, $template, $display);
    }

    public function getPublication(): Publication
    {
        return $this->publication;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getServiceFileLocale(): string
    {
        return $this->serviceFileLocale;
    }

    public function getSubmission(): Submission
    {
        return $this->submission;
    }

    /**
     * Reads user input data from the request
     */
    public function readInputData(): void
    {
        $this->readUserVars(['serviceFile', 'serviceFileLocale', 'stageId', 'serviceType']);
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function setServiceFileLocale(string $serviceFileLocale): void
    {
        $this->serviceFileLocale = $serviceFileLocale;
    }

    /**
     * Handles the download of ORKG file
     */
    private function downloadServiceFile(ServiceFileHandler $handler): void
    {
        $handler->setPublication($this->getPublication());
        $handler->setServiceFile($this->getData('serviceFile'));
        $handler->setServiceFilelocale($this->getData('serviceFileLocale'));
        $handler->setStageId($this->getData('stageId'));
        $handler->setSubmission($this->getSubmission());
        $handler->processSubmissionAndRedirect();
    }
    public function getListOfServices(): array
    {
        return $this->listOfServices;
    }

    public function getServiceType(): string
    {
        return $this->getData('serviceType');
    }
    function handle404(string $message): void
    {
        header('HTTP/1.0 404 Not Found');
        fatalError($message);
    }

}
