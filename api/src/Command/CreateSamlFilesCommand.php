<?php

// src/Command/CreateUserCommand.php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Twig\Environment;

class CreateSamlFilesCommand extends Command
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:saml:generate')
            // the short description shown while running "php bin/console list"
            ->setDescription('generates saml files');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        /** @var string $version */

        $fileSystem = New Filesystem();

        $output->writeln('Generating config.yaml');
        $config = $this->twig->render('saml/config.yaml.twig');
        $fileSystem->dumpFile('config/packages/config.yaml', $config);
        $io->success(sprintf('Data written to %s/config.yaml.', '/app/config/packages'));

        return 0;
    }
}
