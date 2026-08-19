export default function LoadingScreen() {
  return (
    <div className="flex min-h-[60vh] items-center justify-center">
      <div className="flex flex-col items-center gap-4">
        <div className="h-10 w-10 animate-spin rounded-full border-2 border-soft-red border-t-crimson" />
        <p className="text-sm tracking-widest text-charcoal/40 uppercase">Loading</p>
      </div>
    </div>
  );
}
