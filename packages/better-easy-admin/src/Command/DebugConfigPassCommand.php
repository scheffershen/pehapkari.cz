<?php declare(strict_types=1);

namespace OpenProject\BetterEasyAdmin\Command;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigManager;
use EasyCorp\Bundle\EasyAdminBundle\Configuration\ConfigPassInterface;
use Nette\Utils\Strings;
use ReflectionClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\PackageBuilder\Console\Command\CommandNaming;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;

final class DebugConfigPassCommand extends Command
{
    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var PrivatesAccessor
     */
    private $privatesAccessor;

    public function __construct(
        ConfigManager $configManager,
        SymfonyStyle $symfonyStyle,
        PrivatesAccessor $privatesAccessor
    ) {
        parent::__construct();
        $this->configManager = $configManager;
        $this->symfonyStyle = $symfonyStyle;
        $this->privatesAccessor = $privatesAccessor;
    }

    protected function configure(): void
    {
        $this->setName(CommandNaming::classToName(self::class));
        $this->setDescription(sprintf('Show "%s" implementation in their priority order', ConfigPassInterface::class));
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $configPasses = $this->privatesAccessor->getPrivateProperty($this->configManager, 'configPasses');

        $this->symfonyStyle->section('Active config passes in order of execution');

        foreach ($configPasses as $configPass) {
            $bareClass = Strings::after(get_class($configPass), '\\', -1);
            $mainNamespace = Strings::before(get_class($configPass), '\\', 1);

            $reflectionClass = new ReflectionClass($configPass);
            $filePath = $reflectionClass->getFileName();

            $this->symfonyStyle->writeln(sprintf(' * %s (%s) - %s', $bareClass, $mainNamespace, $filePath));
        }

        $this->symfonyStyle->newLine();
    }
}
