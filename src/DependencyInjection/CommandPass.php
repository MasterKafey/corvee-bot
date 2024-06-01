<?php

namespace App\DependencyInjection;

use App\Discord\Handler\CommandHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CommandPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(CommandHandler::class)) {
            return;
        }

        $commandHandler = $container->getDefinition(CommandHandler::class);
        $commands = $container->findTaggedServiceIds('app.discord.command');

        foreach ($commands as $id => $tags) {
            $commandHandler
                ->addMethodCall('addCommand', [new Reference($id)])
            ;
        }
    }
}
