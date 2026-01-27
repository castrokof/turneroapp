# Add project specific ProGuard rules here.
-keepclassmembers class * {
    @android.webkit.JavascriptInterface <methods>;
}
-keepattributes JavascriptInterface
-keep public class com.turnero.tv.** { *; }
