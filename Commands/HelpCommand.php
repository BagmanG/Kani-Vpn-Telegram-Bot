<?php
class HelpCommand extends Command
{
    public function __construct(string $path)
    {
        $this->setPath($path);
    }

    public function run()
    {
        DatabaseEventer::OnChangeUserState("support");
        Telegram::sendMessage("Если у вас возникли вопросы, вы можете задать их здесь. В ближайшее время мы вам ответим!");
    }
} 
?>