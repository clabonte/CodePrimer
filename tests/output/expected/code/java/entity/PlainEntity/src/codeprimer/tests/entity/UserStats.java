/*
 * This file has been generated by CodePrimer.io
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

package codeprimer.tests.entity;

/**
 * Class UserStats
 * Simple statistics about the user
 * @package codeprimer.tests.entity
 */
public class UserStats
{
    /** First time the user logged in the system */
    private Date firstLogin = null;

    /** Last time the user logged in the system */
    private Date lastLogin = null;

    /** Number of time the user logged in the system */
    private long loginCount = null;

    /**
     * UserStats default constructor
     */
    public UserStats() {}

    /**
     * @param firstLogin
     * @return UserStats
     */
    public UserStats setFirstLogin(Date firstLogin) {
        this.firstLogin = firstLogin;
        return this;
    }

    /**
     * @return Date
     */
    public Date getFirstLogin() {
        return this.firstLogin;
    }

    /**
     * @param lastLogin
     * @return UserStats
     */
    public UserStats setLastLogin(Date lastLogin) {
        this.lastLogin = lastLogin;
        return this;
    }

    /**
     * @return Date
     */
    public Date getLastLogin() {
        return this.lastLogin;
    }

    /**
     * @param loginCount
     * @return UserStats
     */
    public UserStats setLoginCount(long loginCount) {
        this.loginCount = loginCount;
        return this;
    }

    /**
     * @return long
     */
    public long getLoginCount() {
        return this.loginCount;
    }

}
