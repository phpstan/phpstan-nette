<?php declare(strict_types = 1);

namespace PHPStan\Nette;

use Nette\Application\InvalidPresenterException;
use Nette\Application\IPresenter;
use Nette\Application\IPresenterFactory;
use Nette\Application\PresenterFactory;
use PHPStan\Exceptions\PresenterResolvingException;
use PHPStan\Exceptions\PresenterResolvingNotAvailableException;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\ShouldNotHappenException;
use ReflectionClass;
use ReflectionException;
use function count;
use function is_array;
use function is_callable;
use function is_string;
use function preg_match;
use function preg_replace;
use function sprintf;
use function str_replace;
use function strrpos;
use function substr;

class PresenterResolver
{

	/** @var array<string, string|array{0: string, 1: string, 2: string}> */
	protected $mapping;

	/** @var ContainerResolver */
	private $containerResolver;

	/** @var ?ReflectionProvider */
	private $reflectionProvider;

	/** @var IPresenterFactory */
	private $presenterFactory;

	/**
	 * @param array<string, string|array{0: string, 1: string, 2: string}> $mapping
	 */
	public function __construct(array $mapping, ContainerResolver $containerResolver, ?ReflectionProvider $reflectionProvider)
	{
		$this->mapping = $mapping;
		$this->containerResolver = $containerResolver;
		$this->reflectionProvider = $reflectionProvider;
	}

	protected function getPresenterFactory(): IPresenterFactory
	{
		if ($this->presenterFactory === null) {
			if ($this->mapping !== []) {
				$this->presenterFactory = new PresenterFactory();
				$this->presenterFactory->setMapping($this->mapping);
			} elseif ($this->containerResolver->getContainer() !== null) {
				$this->presenterFactory = $this->containerResolver->getContainer()->getByType(IPresenterFactory::class);
			} else {
				throw new PresenterResolvingNotAvailableException(
					'Cannot resolve presenter, no mapping is defined.' .
					' Please provide explicit mappings in parameters.nette.applicationMapping or use parameters.nette.containerLoader to load it automatically.'
				);
			}
		}
		return $this->presenterFactory;
	}

	/**
	 * @return array<string, array{0: string, 1: string, 2: string}>
	 * @throws ShouldNotHappenException
	 * @throws ReflectionException
	 */
	protected function getCurrentMapping(): array
	{
		if ($this->mapping !== []) {
			$convertedMapping = [];
			foreach ($this->mapping as $module => $mask) {
				if (is_string($mask)) {
					if (preg_match('#^\\\\?([\w\\\\]*\\\\)?(\w*\*\w*?\\\\)?([\w\\\\]*\*\w*)$#D', $mask, $m) !== 1) {
						throw new ShouldNotHappenException(sprintf("Invalid mapping mask '%s' in parameters.nette.applicationMapping.", $mask));
					}
					$convertedMapping[$module] = [$m[1], $m[2] !== '' ? $m[2] : '*Module\\', $m[3]];
				} elseif (is_array($mask) && count($mask) === 3) { /** @phpstan-ignore-line */
					$convertedMapping[$module] = [$mask[0] !== '' ? $mask[0] . '\\' : '', $mask[1] . '\\', $mask[2]];
				} else {
					throw new PresenterResolvingException(sprintf('Invalid mapping mask for module %s in parameters.nette.applicationMapping.', $module));
				}
			}
			return $convertedMapping;
		}

		return $this->extractMappingFromPresenterFactory($this->getPresenterFactory());
	}

	public function getPresenterClassByName(string $name, ?string $currentPresenterClass = null): string
	{
		if ($name === 'this' && $currentPresenterClass !== null) {
			return $currentPresenterClass;
		}

		try {
			$name = $this->resolvePresenterName($name, $currentPresenterClass);
			$presenterClass = $this->getPresenterClass($name);
		} catch (InvalidPresenterException $e) {
			throw new PresenterResolvingException($e->getMessage(), $e->getCode(), $e);
		}

		if ($this->reflectionProvider !== null) {
			if (!$this->reflectionProvider->hasClass($presenterClass)) {
				throw new PresenterResolvingException(sprintf("Cannot load presenter '%s', class '%s' was not found.", $name, $presenterClass));
			}

			$presenterClassReflection = $this->reflectionProvider->getClass($presenterClass);

			if (!$presenterClassReflection->implementsInterface(IPresenter::class)) {
				throw new PresenterResolvingException(sprintf("Cannot load presenter '%s', class '%s' is not Nette\\Application\\IPresenter implementor.", $name, $presenterClass));
			}
		}

		return $presenterClass;
	}

	public function resolvePresenterName(string $name, ?string $currentPresenterClass = null): string
	{
		if ($name[0] === ':') {
			return substr($name, 1);
		} elseif ($currentPresenterClass === null) {
			throw new PresenterResolvingException(sprintf("Cannot resolve relative presenter name '%s' - current presenter is not set.", $name));
		}

		$currentName = $this->unformatPresenterClass($currentPresenterClass);
		$currentNameSepPos = strrpos($currentName, ':');
		if ($currentNameSepPos !== false && $currentNameSepPos !== 0) {
			$currentModule = substr($currentName, 0, $currentNameSepPos);
			$currentPresenter = substr($currentName, $currentNameSepPos + 1);
		} else {
			$currentModule = '';
			$currentPresenter = $currentName;
		}

		if ($name === 'this') {
			return $currentModule . ':' . $currentPresenter;
		}

		return $currentModule . ':' . $name;
	}

	/**
	 * @return array<string, array{0: string, 1: string, 2: string}>
	 * Override just this method if you use non-standard PresenterFactory but with standard mapping logic to extract mapping from it.
	 */
	protected function extractMappingFromPresenterFactory(object $presenterFactory)
	{
		if (!$presenterFactory instanceof PresenterFactory) {
			throw new PresenterResolvingException(
				'PresenterFactory in your container is not instance of Nette\Application\PresenterFactory. We cannot get mapping from it.' .
				' Either set your mappings explicitly in parameters.nette.applicationMapping ' .
				' or replace service nettePresenterResolver with your own override of getPresenterClass() and/or unformatPresenterClass().'
			);
		}

		$presenterFactoryMappingProperty = 'mapping';

		$mappingPropertyReflection = (new ReflectionClass($presenterFactory))->getProperty($presenterFactoryMappingProperty);
		$mappingPropertyReflection->setAccessible(true);
		/** @var array<string, array{0: string, 1: string, 2: string}> $mapping */
		$mapping = $mappingPropertyReflection->getValue($presenterFactory);

		return $mapping;
	}

	/**
	 * Convert presenter name to presenter class name (for example MyModule:Homepage to \App\Presenters\MyModule\HomepagePresenter)
	 * Override this method if you use non-standard PresenterFactory with custom mapping logic.
	 */
	protected function getPresenterClass(string $name): string
	{
		if (is_callable([$this->getPresenterFactory(), 'formatPresenterClass'])) {
			return $this->getPresenterFactory()->formatPresenterClass($name);
		}

		return $this->getPresenterFactory()->getPresenterClass($name);
	}

	/**
	 * Convert presenter class name to presenter name (for example \App\Presenters\MyModule\HomepagePresenter to MyModule:Homepage
	 * Override this method if you use non-standard PresenterFactory with custom mapping logic.
	 */
	protected function unformatPresenterClass(string $class): string
	{
		$presenterName = null;

		if (is_callable([$this->getPresenterFactory(), 'unformatPresenterClass'])) {
			// silenced because it can throw deprecated error in some versions of Nette
			$presenterName = @$this->getPresenterFactory()->unformatPresenterClass($class);
		} else {
			foreach ($this->getCurrentMapping() as $module => $mapping) {
				$mapping = str_replace(['\\', '*'], ['\\\\', '(\w+)'], $mapping);
				if (preg_match('#^\\\\?' . $mapping[0] . '((?:' . $mapping[1] . ')*)' . $mapping[2] . '$#Di', $class, $matches) !== 1) {
					continue;
				}

				$presenterName = ($module === '*' ? '' : $module . ':')
					. preg_replace('#' . $mapping[1] . '#iA', '$1:', $matches[1]) . $matches[3];
			}
		}

		if ($presenterName === null) {
			throw new PresenterResolvingException(sprintf("Cannot convert presenter class '%s' to presenter name. No matching mapping found.", $class));
		}

		return $presenterName;
	}

}
