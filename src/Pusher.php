<?php

namespace ElfSundae\XgPush;

use Illuminate\Support\Arr;

class Pusher
{
    /**
     * The XingeApp instance.
     *
     * @var \ElfSundae\XgPush\XingeApp
     */
    protected $xinge;

    /**
     * The pusher environment.
     *
     * 向iOS设备推送时必填，1表示推送生产环境；2表示推送开发环境。推送Android平台不填或填0.
     *
     * @var int
     */
    protected $environment = XingeApp::IOSENV_DEV;

    /**
     * The key of custom payload.
     *
     * @var string
     */
    protected $customKey;

    /**
     * Xinge account prefix.
     *
     * @warning 信鸽不允许使用简单的账号，例如纯数字的id。
     *
     * @var string
     */
    protected $accountPrefix;

    /**
     * Create a new instance.
     *
     * @param  string  $appKey
     * @param  string  $appSecret
     */
    public function __construct($appKey, $appSecret)
    {
        $this->xinge = new XingeApp($appKey, $appSecret);
    }

    /**
     * Get the XingeApp instance.
     *
     * @return \ElfSundae\XgPush\XingeApp
     */
    public function xinge()
    {
        return $this->xinge;
    }

    /**
     * Get the app key.
     *
     * @return string
     */
    public function getAppKey()
    {
        return $this->xinge->accessId;
    }

    /**
     * Get the app secret.
     *
     * @return string
     */
    public function getAppSecret()
    {
        return $this->xinge->secretKey;
    }

    /**
     * Get the pusher environment.
     *
     * @return int
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Set the pusher environment.
     *
     * @param  mixed  $env
     * @return $this
     */
    public function setEnvironment($env)
    {
        if (is_string($env)) {
            $env = $env == 'production' ? XingeApp::IOSENV_PROD : XingeApp::IOSENV_DEV;
        }

        if (is_int($env)) {
            $this->environment = $env;
        }

        return $this;
    }

    /**
     * Get the key of custom payload.
     *
     * @return string|null
     */
    public function getCustomKey()
    {
        return $this->customKey;
    }

    /**
     * Set the key of custom payload.
     *
     * @param  string|null  $key
     * @return $this
     */
    public function setCustomKey($key)
    {
        $this->customKey = $key;

        return $this;
    }

    /**
     * Get the account prefix.
     *
     * @return string
     */
    public function getAccountPrefix()
    {
        return $this->accountPrefix;
    }

    /**
     * Set the account prefix.
     *
     * @param  string  $prefix
     * @return $this
     */
    public function setAccountPrefix($prefix)
    {
        $this->accountPrefix = $prefix;

        return $this;
    }

    /**
     * Determine if the Xinge response is success.
     *
     * @see http://developer.qq.com/wiki/xg/%E6%9C%8D%E5%8A%A1%E7%AB%AFAPI%E6%8E%A5%E5%85%A5/Rest%20API%20%E4%BD%BF%E7%94%A8%E6%8C%87%E5%8D%97/Rest%20API%20%E4%BD%BF%E7%94%A8%E6%8C%87%E5%8D%97.html
     *
     * @param  mixed  $response
     * @return bool
     */
    public function succeed($response)
    {
        return $this->code($response) === 0;
    }

    /**
     * Get the code of Xinge response.
     *
     * @param  mixed  $response
     * @return int|null
     */
    public function code($response)
    {
        if (is_array($response)) {
            return Arr::get($response, 'ret_code');
        }
    }

    /**
     * Get the error message of Xinge response.
     *
     * @param  mixed  $response
     * @return string|null
     */
    public function message($response)
    {
        if (is_array($response)) {
            return Arr::get($response, 'err_msg');
        }
    }

    /**
     * Get the result data of Xinge response.
     *
     * @param  mixed  $response
     * @param  string  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function result($response, $key = null, $default = null)
    {
        if (is_array($response)) {
            return Arr::get($response, $key ? "result.{$key}" : 'result', $default);
        }
    }

    /**
     * Encode the custom data.
     *
     * @param  mixed  $data
     * @return array
     */
    public function encodeCustomData($data)
    {
        if ($this->customKey && $data) {
            return [$this->customKey => $data];
        }

        return $data ?: [];
    }

    /**
     * Get Xinge account for the given user.
     *
     * @param  mixed  $user
     * @return string
     */
    public function accountForUser($user)
    {
        if ($this->accountPrefix && is_string($user) && starts_with($user, $this->accountPrefix)) {
            return $user;
        }

        if (is_object($user)) {
            $user = $user->id;
        } elseif (is_array($user)) {
            $user = $user['id'];
        }

        return $this->accountPrefix.$user;
    }

    /**
     * Get Xinge accounts for users.
     *
     * @param  array $users
     * @return array
     */
    public function accountsForUsers(array $users)
    {
        return array_map([$this, 'accountForUser'], $users);
    }

    /**
     * Creates a new MessageIOS instance.
     *
     * @param  string  $alert
     * @param  mixed  $custom
     * @param  int  $badge
     * @param  string  $sound
     * @return \ElfSundae\XgPush\MessageIOS
     */
    public function createIOSMessage($alert = '', $custom = null, $badge = 1, $sound = 'default')
    {
        $message = new MessageIOS();
        $message->setAlert($alert);
        $message->setCustom($this->encodeCustomData($custom));
        if (is_int($badge) && $badge >= 0) {
            $message->setBadge($badge);
        }
        if ($sound) {
            $message->setSound($sound);
        }

        return $message;
    }

    /**
     * Create a new Message instance.
     *
     * @param  string  $title
     * @param  string  $content
     * @param  mixed  $custom
     * @param  int  $type
     * @return \ElfSundae\XgPush\Message
     */
    public function createAndroidMessage($title = '', $content = '', $custom = null, $type = Message::TYPE_MESSAGE)
    {
        $message = new Message();
        $message->setTitle($title);
        $message->setContent($content);
        $message->setCustom($this->encodeCustomData($custom));
        $message->setType($type);

        return $message;
    }

    /**
     * Create a new Message instance for notification.
     * The default action is opening app.
     *
     * @param  string  $title
     * @param  string  $content
     * @param  mixed  $custom
     * @return \ElfSundae\XgPush\Message
     */
    public function createAndroidNotification($title = '', $content = '', $custom = null)
    {
        $message = $this->createAndroidMessage($title, $content, $custom, Message::TYPE_NOTIFICATION);

        $message->setStyle(new Style(0, 1, 1, 1, 0));
        $action = new ClickAction();
        $action->setActionType(ClickAction::TYPE_ACTIVITY);
        $message->setAction($action);

        return $message;
    }

    /**
     * Push message to a device.
     *
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @param  string  $deviceToken
     * @return array
     */
    public function toDevice($message, $deviceToken)
    {
        return $this->xinge->PushSingleDevice($deviceToken, $message, $this->environment);
    }

    /**
     * Push message to all devices.
     *
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @return array
     */
    public function toAllDevices($message)
    {
        return $this->xinge->PushAllDevices(0, $message, $this->environment);
    }

    /**
     * Push message to users.
     *
     * @warning 用户数限制 100 个。
     *
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @param  mixed  $users
     * @return array
     */
    public function toUser($message, $users)
    {
        $users = $this->getParameterAsArray(func_get_args(), 1);
        $accounts = $this->accountsForUsers($users);

        if (count($accounts) == 1) {
            return $this->xinge->PushSingleAccount(0, $accounts[0], $message, $this->environment);
        }

        return $this->xinge->PushAccountList(0, $accounts, $message, $this->environment);
    }

    /**
     * Push message to tagged devices.
     *
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @param  string|string[]  $tags
     * @param  string  $tagsOperation  'OR', 'AND'
     * @return array
     */
    public function toTags($message, $tags, $tagsOperation = 'OR')
    {
        return $this->xinge->PushTags(0, (array) $tags, strtoupper($tagsOperation), $message, $this->environment);
    }

    /**
     * Create a batch push.
     *
     * @param  \ElfSundae\XgPush\Message|\ElfSundae\XgPush\MessageIOS  $message
     * @return string|null
     */
    public function createBatch($message)
    {
        return $this->result($this->xinge->CreateMultipush($message, $this->environment), 'push_id');
    }

    /**
     * Batch pushing to a list of users.
     *
     * @warning 用户数限制 1000 个。
     *
     * @param  int|string  $pushId
     * @param  mixed $users
     * @return array
     */
    public function batchToUsers($pushId, $users)
    {
        return $this->xinge->PushAccountListMultiple(
            $pushId,
            $this->accountsForUsers($this->getParameterAsArray(func_get_args(), 1))
        );
    }

    /**
     * Batch pushing to a list of devices.
     *
     * @warning 设备数限制 1000 个。
     *
     * @param  int|string  $pushId
     * @param  mixed  $deviceTokens
     * @return array
     */
    public function batchToDevices($pushId, $deviceTokens)
    {
        return $this->xinge->PushDeviceListMultiple(
            $pushId,
            $this->getParameterAsArray(func_get_args(), 1)
        );
    }

    /**
     * Query group pushing status.
     *
     * @param  mixed  $pushIds
     * @return array
     */
    public function queryPushStatus($pushIds)
    {
        $pushIds = $this->getParameterAsArray(func_get_args());

        $list = $this->result($this->xinge->QueryPushStatus($pushIds), 'list', []);

        return array_combine(array_pluck($list, 'push_id'), $list);
    }

    /**
     * Cancel a timed pushing task that has not been pushed.
     *
     * @param  string  $pushId
     * @return array
     */
    public function cancelTimedPush($pushId)
    {
        return $this->xinge->CancelTimingPush($pushId);
    }

    /**
     * Query all device tokens for the given user.
     *
     * @param  mixed  $user
     * @return string[]
     */
    public function queryDeviceTokensForUser($user)
    {
        return $this->result($this->xinge->QueryTokensOfAccount($this->accountForUser($user)), 'tokens', []);
    }

    /**
     * Delete device tokens for the given user.
     *
     * @param  mixed  $user
     * @param  string|string[]  $deviceTokens
     * @return bool
     */
    public function deleteDeviceTokensForUser($user, $deviceTokens = null)
    {
        $account = $this->accountForUser($user);

        if (is_null($deviceTokens)) {
            return $this->succeed($this->xinge->DeleteAllTokensOfAccount($account));
        }

        $deviceTokens = array_unique((array) $deviceTokens);

        $result = true;

        foreach ($deviceTokens as $token) {
            $result = $result && $this->succeed($this->xinge->DeleteTokenOfAccount($account, $token));
        }

        return $result;
    }

    /**
     * Query count of registered devices.
     *
     * @return int
     */
    public function queryCountOfDevices()
    {
        return $this->result($this->xinge->QueryDeviceCount(), 'device_num', 0);
    }

    /**
     * Query info for the given device token.
     *
     * @param  string  $deviceToken
     * @return array
     */
    public function queryDeviceTokenInfo($deviceToken)
    {
        return $this->xinge->QueryInfoOfToken($deviceToken);
    }

    /**
     * Query count of registered tokens for the given tag.
     *
     * @param  string  $tag
     * @return int
     */
    public function queryCountOfDeviceTokensForTag($tag)
    {
        return $this->result($this->xinge->QueryTagTokenNum($tag), 'device_num', 0);
    }

    /**
     * Query tags.
     *
     * @return array
     */
    public function queryTags($start = 0, $limit = 100)
    {
        return $this->xinge->QueryTags($start, $limit);
    }

    /**
     * Query all tags for the given device token.
     *
     * @param  string  $deviceToken
     * @return string[]
     */
    public function queryTagsForDeviceToken($deviceToken)
    {
        return $this->result($this->xinge->QueryTokenTags($deviceToken), 'tags', []);
    }

    /**
     * Query all tags for the given user.
     *
     * @param  mixed  $user
     * @param  array  &$deviceTokens
     * @return array
     */
    public function queryTagsForUser($user, &$deviceTokens = null)
    {
        $deviceTokens = $this->queryDeviceTokensForUser($user);

        $result = [];
        foreach ($deviceTokens as $token) {
            $result[$token] = $this->queryTagsForDeviceToken($token);
        }

        return $result;
    }

    /**
     * Add tags for device tokens.
     *
     * @warning 每次最多设置 20 对。
     *
     * @param  \ElfSundae\XgPush\TagTokenPair[]  $tagTokenPairs
     * @return bool
     */
    public function addTags($tagTokenPairs)
    {
        return $this->succeed($this->xinge->BatchSetTag($tagTokenPairs));
    }

    /**
     * Add tags for the given device token.
     *
     * @param  string  $deviceToken
     * @param  mixed  $tags
     * @return bool
     */
    public function addTagsForDeviceToken($deviceToken, $tags)
    {
        return $this->addTags(
            $this->createTagTokenPairs($this->getParameterAsArray(func_get_args(), 1), $deviceToken)
        );
    }

    /**
     * Add tags for the given user.
     *
     * @param  mixed  $user
     * @param  mixed  $tags
     * @return bool
     */
    public function addTagsForUser($user, $tags)
    {
        return $this->addTags(
            $this->createTagTokenPairs(
                $this->getParameterAsArray(func_get_args(), 1),
                $this->queryDeviceTokensForUser($user)
            )
        );
    }

    /**
     * Remove tags for device tokens.
     *
     * @warning 每次最多删除 20 对。
     *
     * @param  \ElfSundae\XgPush\TagTokenPair[]  $tagTokenPairs
     * @return bool
     */
    public function removeTags($tagTokenPairs)
    {
        return $this->succeed($this->xinge->BatchDelTag($tagTokenPairs));
    }

    /**
     * Remove tags for the given device token.
     *
     * @param  string  $deviceToken
     * @param  mixed  $tags
     * @return bool
     */
    public function removeTagsForDeviceToken($deviceToken, $tags)
    {
        return $this->removeTags(
            $this->createTagTokenPairs($this->getParameterAsArray(func_get_args(), 1), $deviceToken)
        );
    }

    /**
     * Remove tags for the given user.
     *
     * @param  mixed  $user
     * @param  mixed  $tags
     * @return bool
     */
    public function removeTagsForUser($user, $tags)
    {
        return $this->removeTags(
            $this->createTagTokenPairs(
                $this->getParameterAsArray(func_get_args(), 1),
                $this->queryDeviceTokensForUser($user)
            )
        );
    }

    /**
     * Set tags for the given device token.
     *
     * @param  string  $deviceToken
     * @param  mixed  $tags
     * @return bool
     */
    public function setTagsForDeviceToken($deviceToken, $tags)
    {
        $tags = $this->getParameterAsArray(func_get_args(), 1);
        $oldTags = $this->queryTagsForDeviceToken($deviceToken);

        $result = true;

        if ($addTags = array_diff($tags, $oldTags)) {
            $result = $result && $this->addTagsForDeviceToken($deviceToken, $addTags);
        }

        if ($removeTags = array_diff($oldTags, $tags)) {
            $result = $result && $this->removeTagsForDeviceToken($deviceToken, $removeTags);
        }

        return $result;
    }

    /**
     * Set tags for the given user.
     *
     * @param  mixed  $user
     * @param  mixed  $tags
     * @return bool
     */
    public function setTagsForUser($user, $tags)
    {
        $tags = $this->getParameterAsArray(func_get_args(), 1);
        $oldTags = $this->queryTagsForUser($user, $tokens);

        $addTagTokenPairs = [];
        $removeTagTokenPairs = [];

        foreach ($oldTags as $token => $tokenTags) {
            $addTagTokenPairs = array_merge($addTagTokenPairs,
                $this->createTagTokenPairs(array_diff($tags, $tokenTags), $token)
            );

            $removeTagTokenPairs = array_merge($removeTagTokenPairs,
                $this->createTagTokenPairs(array_diff($tokenTags, $tags), $token)
            );
        }

        $result = true;

        if (count($addTagTokenPairs) > 0) {
            $result = $result && $this->addTags($addTagTokenPairs);
        }

        if (count($removeTagTokenPairs) > 0) {
            $result = $result && $this->removeTags($removeTagTokenPairs);
        }

        return $result;
    }

    /**
     * Get parameter as array.
     *
     * @param  array  $args
     * @param  int $offset
     * @return array
     */
    protected function getParameterAsArray(array $args, $offset = 0)
    {
        return is_array($args[$offset]) ? $args[$offset] : array_slice($args, $offset);
    }

    /**
     * Create array of TagTokenPair.
     *
     * @warning $tags 和 $tokens 一个是数组，另一个是字符串
     *
     * @param  string|string[]  $tags
     * @param  string|string[]  $tokens
     * @return \ElfSundae\XgPush\TagTokenPair[]
     */
    protected function createTagTokenPairs($tags, $tokens)
    {
        $tagTokenPairs = [];

        $tokens = (array) $tokens;
        foreach ((array) $tags as $tag) {
            foreach ($tokens as $token) {
                $tagTokenPairs[] = new TagTokenPair($tag, $token);
            }
        }

        return $tagTokenPairs;
    }

    /**
     * Dynamically handle calls to the XingeApp instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->xinge, $method], $parameters);
    }
}
