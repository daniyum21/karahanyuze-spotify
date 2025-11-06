# How to Clear Browser Cache on iPhone

## Safari (iPhone Default Browser)

### Method 1: Clear History and Website Data (Recommended)
1. Open **Settings** app
2. Scroll down and tap **Safari**
3. Scroll down and tap **Clear History and Website Data**
4. Tap **Clear History and Data** to confirm
5. This will clear:
   - Browsing history
   - Cached website data
   - Cookies
   - Saved passwords (if you choose)

### Method 2: Clear Cache Only (Less Disruptive)
1. Open **Settings** app
2. Scroll down and tap **Safari**
3. Tap **Advanced**
4. Tap **Website Data**
5. Tap **Remove All Website Data**
6. Tap **Remove Now** to confirm
   - This clears cache but keeps browsing history and passwords

### Method 3: Hard Refresh (Quick Fix)
1. Open Safari
2. Go to `https://iwacu.org`
3. Press and hold the **Refresh** button (circular arrow in address bar)
4. Tap **Reload Without Content Blockers** or just **Reload**

## Other Browsers on iPhone

### Chrome
1. Open Chrome
2. Tap the **three dots** (⋮) in bottom right
3. Tap **Settings**
4. Tap **Privacy and Security**
5. Tap **Clear Browsing Data**
6. Select **Cached images and files**
7. Tap **Clear Browsing Data**

### Firefox
1. Open Firefox
2. Tap **Settings** (gear icon)
3. Tap **Clear Private Data**
4. Select **Cache**
5. Tap **Clear Private Data**

## Alternative: Use Private/Incognito Mode
1. Open Safari
2. Tap the **Tabs** button (square icon)
3. Tap **Private** (bottom left)
4. Navigate to `https://iwacu.org`
5. This uses a fresh session without cached data

## After Clearing Cache
1. Hard refresh the page: Press and hold the refresh button, then tap **Reload**
2. If still redirecting, try:
   - Closing and reopening Safari
   - Restarting your iPhone
   - Waiting a few minutes for cache to expire

## Why This Happens
- Browsers cache redirects (301/302) for performance
- The old redirect to `/home4/biriheco/public_html/iwacu/%25%25` was cached
- The server now sends cache-control headers to prevent this
- After clearing cache, the new configuration will be used

